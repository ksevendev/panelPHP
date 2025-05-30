<?php

declare(strict_types = 1);

namespace App\Filament\Components;

use Closure;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

class PtbrMoney extends TextInput
{
    protected string|int|float|null $initialValue = '0,00';

    protected function setUp(): void
    {
        $this
            ->prefix('R$')
            ->maxLength(13)
            ->extraAlpineAttributes([

                'x-on:keypress' => 'function() {
                        var charCode = event.keyCode || event.which;
                        if (charCode < 48 || charCode > 57) {
                            event.preventDefault();
                            return false;
                        }
                        return true;
                    }',

                'x-on:keyup' => 'function() {
                        var money = $el.value.replace(/\D/g, "");
                        money = (money / 100).toFixed(2) + "";
                        money = money.replace(".", ",");
                        money = money.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
                        money = money.replace(/(\d)(\d{3}),/g, "$1.$2,");

                        $el.value = money;
                        $el.dispatchEvent(new Event(\'input\'));
                    }',
            ])
            ->dehydrateMask()
            ->default(0.00)
            ->formatStateUsing(function ($state) {
                return $state ? number_format(floatval($state), 2, ',', '.') : $this->initialValue;
            });
    }

    public function dehydrateMask(bool|Closure $condition = true): static
    {
        if ($condition) {
            $this->dehydrateStateUsing(
                fn ($state): ?float => $state ?
                    floatval(
                        Str::of($state)
                            ->replace('.', '')
                            ->replace(',', '.')
                            ->toString()
                    ) :
                    null
            );
        } else {
            $this->dehydrateStateUsing(null);
        }

        return $this;
    }

    public function initialValue(null|string|int|float|Closure $value = '0,00'): static
    {
        $this->initialValue = $value;

        return $this;
    }
}
