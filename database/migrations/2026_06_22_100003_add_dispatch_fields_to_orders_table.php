<?php

use App\Models\Order;
use App\Services\BarcodeService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'order_barcode')) {
                $table->string('order_barcode')->nullable()->unique()->after('order_number');
            }
            if (! Schema::hasColumn('orders', 'shipping_status')) {
                $table->string('shipping_status')->default('pending')->after('order_status');
            }
            if (! Schema::hasColumn('orders', 'courier_company_id')) {
                $table->foreignId('courier_company_id')->nullable()->after('shipping_status')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('orders', 'courier_name')) {
                $table->string('courier_name')->nullable()->after('courier_company_id');
            }
            if (! Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('courier_name');
            }
            if (! Schema::hasColumn('orders', 'dispatched_at')) {
                $table->timestamp('dispatched_at')->nullable()->after('tracking_number');
            }
            if (! Schema::hasColumn('orders', 'dispatched_by')) {
                $table->string('dispatched_by')->nullable()->after('dispatched_at');
            }
            if (! Schema::hasColumn('orders', 'dispatch_notes')) {
                $table->text('dispatch_notes')->nullable()->after('dispatched_by');
            }
        });

        $barcodeService = app(BarcodeService::class);

        Order::query()->whereNull('order_barcode')->orderBy('id')->each(function (Order $order) use ($barcodeService) {
            $order->update([
                'order_barcode' => $barcodeService->makeOrderBarcode((int) $order->id),
                'shipping_status' => $order->shipping_status ?: 'pending',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'courier_company_id')) {
                $table->dropConstrainedForeignId('courier_company_id');
            }

            $columns = array_filter([
                Schema::hasColumn('orders', 'order_barcode') ? 'order_barcode' : null,
                Schema::hasColumn('orders', 'shipping_status') ? 'shipping_status' : null,
                Schema::hasColumn('orders', 'courier_name') ? 'courier_name' : null,
                Schema::hasColumn('orders', 'tracking_number') ? 'tracking_number' : null,
                Schema::hasColumn('orders', 'dispatched_at') ? 'dispatched_at' : null,
                Schema::hasColumn('orders', 'dispatched_by') ? 'dispatched_by' : null,
                Schema::hasColumn('orders', 'dispatch_notes') ? 'dispatch_notes' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
