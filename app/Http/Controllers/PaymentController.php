<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Commission;
use App\Models\Invoice as InvoiceModel;
use Shetabit\Multipay\Invoice;
use App\Models\Transaction;
use Shetabit\Payment\Facade\Payment;
use Shetabit\Multipay\Exceptions\PurchaseFailedException;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function pay($book_id) {
        $check_book = Book::findOrFail($book_id);
        (empty($check_book) || $check_book->stock < 1) ? abort(404) : null;

        // start db transaction
        DB::beginTransaction();

        $invoice = InvoiceModel::where('user_id', auth()->user()->id)->where('status', InvoiceModel::INVOICE_STATUS_UNPAID)->first();
        if(empty($invoice)) {
            $invoice = new InvoiceModel();
        }

        $invoice->user_id = auth()->user()->id;
        $invoice->book_id = $check_book->id;
        $invoice->amount = $check_book->price;
        $invoice->status = InvoiceModel::INVOICE_STATUS_UNPAID;
        $invoice->description = 'پرداخت' . $check_book->title;
        $invoice->save();

        try {
            return  Payment::callbackUrl(route('payment.result', ['invoiceNumber' => $invoice->id]))->purchase(
                (new invoice)->amount(intval($invoice->amount)),
                function ($driver, $transactionId) use($invoice) {

                    Transaction::makeTransaction($transactionId, $invoice->id);
                    // done db transaction
                    DB::commit();
                }
            )->pay()->render();
        } catch (PurchaseFailedException $exception) {
            logger($exception->getMessage());
            // rollback db transaction
            DB::rollBack();
            return redirect()->back();
        }
    }


    public function paymentResult($invoiceNumber) {
        $check_invoice = InvoiceModel::where('id', $invoiceNumber)
        ->where('status', InvoiceModel::INVOICE_STATUS_UNPAID)
        ->first();
        $refId = null;

        if(empty($check_invoice)) {
            return redirect()->route('home');
        }
        try {
            $transaction = Transaction::where('transaction_id', request()->Authority)
            ->where('invoice_id', $check_invoice->id)
            ->first();
            if(empty($transaction)) {
                return view('auth.subscription')->with('payment_error', 'خطا در پرداخت');
            }
            $receipt = Payment::amount(1000)->transactionId($transaction->transaction_id)->verify();
            $refId = $receipt->getReferenceId();

            DB::beginTransaction();

                $transaction->data = json_encode(["refId" => $refId]);
                $transaction->save();

                $check_invoice->status = InvoiceModel::INVOICE_STATUS_PAID;
                $check_invoice->transaction_id = $transaction->id;
                $check_invoice->save();

                // make commision
                Commission::make_commission($check_invoice);

                // Reduce inventory book
                Book::reduce_inventory($check_invoice->book_id);
            
            DB::commit();

        } catch (InvalidPaymentException $exception) {
            $exception->getMessage();
        }
        return view('auth.resultPayment', compact('refId'));
    }
}
