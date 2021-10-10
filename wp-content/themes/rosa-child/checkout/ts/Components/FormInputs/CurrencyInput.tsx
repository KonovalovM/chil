import React, { useState, useCallback } from 'react';
import MaskedInput from 'react-text-mask'
import createNumberMask from 'text-mask-addons/dist/createNumberMask'

export function CurrencyInput(props: {
  placeholder: string,
  name: string,
  value: string,
  onChange: (value: string) => void;
}) {
  const currencyMask = createNumberMask({
    prefix: '$',
    suffix: '',
    includeThousandsSeparator: true,
    thousandsSeparatorSymbol: ',',
    allowDecimal: true,
    decimalSymbol: '.',
    decimalLimit: 2,
    integerLimit: 7,
    allowNegative: false,
    allowLeadingZeroes: false,
  })

  const onChange = useCallback((event: React.FormEvent<HTMLInputElement>) => {
    props.onChange(event.currentTarget.value);
  }, [props.onChange]);

  return (
    <MaskedInput
      mask={currencyMask}
      type='text'
      inputMode='numeric'
      name={props.name}
      value={props.value}
      onChange={onChange}
    />
  );
}
