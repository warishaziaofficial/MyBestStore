<?php



namespace App\Services\Payments;



class PaymentManager

{

    /** @var array<string, PaymentGatewayInterface> */

    private array $gateways = [];



    public function __construct()

    {

        foreach (config('payments.methods', []) as $key => $method) {

            if (! ($method['active'] ?? true)) {

                continue;

            }



            $driver = $method['driver'] ?? null;



            if ($driver && class_exists($driver)) {

                $this->gateways[$key] = app($driver);

            }

        }

    }



    public function get(string $key): ?PaymentGatewayInterface

    {

        if ($key === 'cod') {

            $key = 'cash_on_delivery';

        }



        return $this->gateways[$key] ?? null;

    }



    /**

     * @return array<int, array{key: string, label: string, configured: bool}>

     */

    public function availableForCheckout(): array

    {

        $methods = [];



        foreach (array_keys(config('payments.methods', [])) as $key) {

            $gateway = $this->get($key);



            if ($gateway) {

                $methods[] = [

                    'key' => $gateway->key(),

                    'label' => $gateway->label(),

                    'configured' => $gateway->isConfigured(),

                ];

            }

        }



        return $methods;

    }

}


