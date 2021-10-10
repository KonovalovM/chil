import React, { useRef, useEffect, useCallback } from 'react';
import { addZeroPadding } from '../../Utilities/formatHelper';
import trim from 'lodash/trim';

interface ExpiryDateInputProps {
  placeholder: string;
  name: string;
  month: number;
  fullYear: number;
  onChange: (month: number, fullYear: number) => void;
}

export function ExpiryDateInput(props: ExpiryDateInputProps) {
  const { placeholder, name, month, fullYear, onChange } = props;
  const expiryDateField = useRef<HTMLInputElement>(null);

  //giving an initial value to the input
  useEffect(() => {
    if (!expiryDateField || !expiryDateField.current) {
      return;
    }
    expiryDateField.current.value = initExpiryValue(month, fullYear);
  }, [expiryDateField, month, fullYear]);

  const handleChangeExpiryDate = useCallback(
    (event: React.ChangeEvent<HTMLInputElement>) => {
      if (!expiryDateField || !expiryDateField.current) {
        return;
      }
      const expiryFormated = formatExpiry(event);
      expiryDateField.current.value = expiryFormated;

      const [newMonth, newFullYear] = parseExpiry(expiryFormated);
      if (newMonth > 0 && newFullYear >= 2000) {
        onChange(newMonth, newFullYear);
      }
    },
    [expiryDateField, onChange]
  );

  return (
    <input
      ref={expiryDateField}
      aria-label="Expiry date in format MM YY"
      autoComplete="cc-exp"
      name={name}
      placeholder={placeholder}
      type="tel"
      onChange={handleChangeExpiryDate}
    />
  );
}

function initExpiryValue(month: number, fullYear: number) {
  if (month <= 0 && fullYear < 2000) {
    return '';
  }

  const year = fullYear - 2000;
  return `${addZeroPadding(month)} / ${addZeroPadding(year)}`;
}

function parseExpiry(formatedExpiry: string) {
  const [monthString, yearString] = formatedExpiry.split('/');
  const month = Number(trim(monthString));
  const year = Number(trim(yearString));
  const fullYear = year !== 0 ? 2000 + year : year;
  return [month, fullYear];
}

function formatExpiry(event: React.ChangeEvent<HTMLInputElement>): string {
  //The eventData is the last key the user press
  const eventData = event.nativeEvent && (event.nativeEvent as unknown as { data: unknown }).data;
  const prevExpiry = event.target.value.split(' / ').join('/');

  if (!prevExpiry) return '';
  let expiry = prevExpiry;
  if (/^[2-9]$/.test(expiry)) {
    expiry = `0${expiry}`;
  }

  if (prevExpiry.length === 2 && +prevExpiry > 12) {
    const [head, ...tail] = prevExpiry.split('');
    expiry = `0${head}/${tail.join('')}`;
  }

  if (/^1[/-]$/.test(expiry)) {
    return `01 / `;
  }

  const expiryData = expiry.match(/(\d{1,2})/g) || [];
  if (expiryData.length === 1) {
    if (!eventData && prevExpiry.includes('/')) {
      return expiryData[0];
    }
    if (/\d{2}/.test(expiry)) {
      return `${expiryData[0]} / `;
    }
  }
  if (expiryData.length > 2) {
    const [, month = null, year = null] = expiryData.join('').match(/^(\d{2}).*(\d{2})$/) || [];
    return [month, year].join(' / ');
  }
  return expiryData.join(' / ');
}
