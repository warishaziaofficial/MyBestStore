<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('customer_id');
            }
            if (! Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }
            if (! Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->nullable();
            }
            if (! Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable();
            }
            if (! Schema::hasColumn('orders', 'city')) {
                $table->string('city')->nullable();
            }
            if (! Schema::hasColumn('orders', 'province')) {
                $table->string('province')->nullable();
            }
            if (! Schema::hasColumn('orders', 'postal_code')) {
                $table->string('postal_code')->nullable();
            }
            if (! Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('orders', 'shipping_amount')) {
                $table->decimal('shipping_amount', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('unpaid');
            }
            if (! Schema::hasColumn('orders', 'order_status')) {
                $table->string('order_status')->default('pending');
            }
            if (! Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        if (Schema::hasColumn('orders', 'shipping') && Schema::hasColumn('orders', 'shipping_amount')) {
            DB::table('orders')->where('shipping_amount', 0)->update([
                'shipping_amount' => DB::raw('shipping'),
            ]);
        }

        if (Schema::hasColumn('orders', 'total') && Schema::hasColumn('orders', 'total_amount')) {
            DB::table('orders')->where('total_amount', 0)->update([
                'total_amount' => DB::raw('total'),
            ]);
        }

        if (Schema::hasColumn('orders', 'status') && Schema::hasColumn('orders', 'order_status')) {
            DB::table('orders')->where('order_status', 'pending')->update([
                'order_status' => DB::raw('status'),
            ]);
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('orders', 'shipping')) {
                $table->dropColumn('shipping');
            }
            if (Schema::hasColumn('orders', 'total')) {
                $table->dropColumn('total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('shipping', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'customer_email',
                'customer_phone',
                'shipping_address',
                'city',
                'province',
                'postal_code',
                'discount_amount',
                'shipping_amount',
                'total_amount',
                'payment_method',
                'payment_status',
                'order_status',
                'notes',
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });
    }
};
