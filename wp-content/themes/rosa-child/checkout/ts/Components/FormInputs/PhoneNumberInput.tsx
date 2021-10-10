import React, { useState, useCallback } from 'react';
import { formatPhoneNumber } from '../../Utilities/formatHelper';

export function PhoneNumberInput(props: { phoneNumber: string; onChange: (phoneNumber: string) => void, required: boolean }) {
  const { onChange, required } = props;

  const [phoneNumber, setPhoneNumber] = useState<string>(props.phoneNumber);

  const handleChange = useCallback(
    (event: React.FormEvent<HTMLInputElement>) => {
      const inputText = event.currentTarget.value;
      const inputTextWithOnlyNumbers = inputText.replace(/[^0-9]/g, '').slice(0, 10);

      onChange(inputTextWithOnlyNumbers);
      setPhoneNumber(inputTextWithOnlyNumbers);
    },
    [phoneNumber, setPhoneNumber, onChange]
  );

  return (
    <input
      type="tel"
      inputMode="numeric"
      pattern="[0-9\-]*"
      placeholder="Contact Number"
      name="contactnumber"
      onChange={handleChange}
      value={formatPhoneNumber(phoneNumber)}
      required={required}
    />
  );
}
