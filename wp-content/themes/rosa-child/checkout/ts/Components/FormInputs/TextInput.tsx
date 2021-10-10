import React, { useState, useCallback } from 'react';

export function TextInput(props: { onChange: (value: string) => void; placeholder: string; name: string; value: string; minLength?: number; maxLength?: number, required?: boolean }) {
  const { onChange, placeholder, name, maxLength } = props;
  const required = props.required !== undefined ? props.required : false;

  const [value, setValue] = useState<string>(props.value);
  const handleChange = useCallback(
    (event: React.FormEvent<HTMLInputElement>) => {
      const inputText = event.currentTarget.value;
      const newText = maxLength ? inputText.slice(0, maxLength) : inputText;

      onChange(newText);
      setValue(newText);
    },
    [value, setValue, onChange]
  );

  return <input type="text" placeholder={placeholder} name={name} onChange={handleChange} value={value} required={required} />;
}