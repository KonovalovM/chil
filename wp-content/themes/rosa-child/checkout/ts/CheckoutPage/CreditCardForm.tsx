// tslint:disable-next-line:no-any
declare const Spreedly: any; //TODO: Add proper type declarations for Spreedly
const spreedlyEnvironmentToken = SPREEDLY_ENVIRONMENT_KEY;

import { SpreedlyError } from '../Common/errorTypes';
import { logError } from '../Utilities/errorLogger';
import classnames from 'classnames';
import React, { useState, useEffect, useCallback } from 'react';
import { CreditCard } from '../Common/PaymentMethod';
import { ExpiryDateInput } from '../Components/FormInputs/ExpiryDateInput';
import { TextInput } from '../Components/FormInputs/TextInput';

interface CreditCardFormProps {
  creditcard: CreditCard | undefined;
  cancel: () => void;
  onSubmit: (creditCard: CreditCard) => void;
}

export function CreditCardForm(props: CreditCardFormProps) {
  const { creditcard, cancel, onSubmit } = props;

  const [fullname, setFullname] = useState<string>(creditcard ? creditcard.fullname : '');
  const [expiryMonth, setExpiryMonth] = useState<number>(creditcard ? creditcard.month : 0);
  const [expiryYear, setExpiryYear] = useState<number>(creditcard ? creditcard.year : 0);
  const [zip, setZip] = useState<string>(creditcard ? creditcard.zip : '');

  const [isNumberInFocus, setIsNumberInFocus] = useState<boolean>(false);
  const [isCvvInFocus, setIsCvvInFocus] = useState<boolean>(false);
  const [errorMessage, setErrorMessage] = useState<string>('');

  //configureing Spreedly
  useEffect(() => {
    Spreedly.init(spreedlyEnvironmentToken, {
      numberEl: 'spreedly-number',
      cvvEl: 'spreedly-cvv',
    });

    //Setting listeners
    Spreedly.on('paymentMethod', (token: string, paymentMethod: Record<string, string>) => {
      onSubmit({
        fullname: String(paymentMethod['full_name']),
        cardtype: String(paymentMethod['card_type']),
        sufix: String(paymentMethod['last_four_digits']),
        year: Number(paymentMethod['year']),
        month: Number(paymentMethod['month']),
        zip: String(paymentMethod['zip']),
        token: String(paymentMethod['token']),
      });
    });

    Spreedly.on('errors', (errors: Array<{ message: string }>) => {
      const newErrorMessage = errors[0]['message'];
      setErrorMessage(newErrorMessage);
      logError(new SpreedlyError(newErrorMessage));
    });

    //Setting styling
    Spreedly.on('ready', () => {
      Spreedly.setPlaceholder('number', 'Card Number');
      Spreedly.setPlaceholder('cvv', 'CVV');
    });

    Spreedly.on('ready', () => {
      Spreedly.setFieldType('number', 'text');
      Spreedly.setNumberFormat('prettyFormat');
      Spreedly.setFieldType('cvv', 'tel');
    });

    Spreedly.on('ready', () => {
      const style = getInputStyle();
      Spreedly.setStyle('number', style);
      Spreedly.setStyle('cvv', style);
    });

    Spreedly.on('fieldEvent', (name: string, type: string, activeEl: string, inputProperties: unknown) => {
      if (name === 'number' && type === 'focus') {
        setIsNumberInFocus(true);
      } else if (name === 'number' && type === 'blur') {
        setIsNumberInFocus(false);
      } else if (name === 'cvv' && type === 'focus') {
        setIsCvvInFocus(true);
      } else if (name === 'cvv' && type === 'blur') {
        setIsCvvInFocus(false);
      }
    });

    return () => {
      Spreedly.reload();
      Spreedly.removeHandlers();
    };
  }, []);

  const getCreditCardToken = useCallback(() => {
    if (!expiryMonth || !expiryYear) {
      throw new Error('Invalid expiration date');
    }
    try {
      Spreedly.tokenizeCreditCard({
        full_name: fullname,
        month: expiryMonth,
        year: expiryYear,
        zip,
      });
    } catch(error) {
      throw new SpreedlyError(error.message);
    }
  }, [expiryMonth, expiryYear, fullname, zip]);

  const updateExpirationDate = useCallback(
    (newMonth: number, newYear: number) => {
      setExpiryMonth(newMonth);
      setExpiryYear(newYear);
    },
    [setExpiryMonth, setExpiryYear]
  );

  const handleOnSubmit = useCallback(
    (event: React.FormEvent<HTMLFormElement>) => {
      event.preventDefault();
      event.stopPropagation();

      try {
        getCreditCardToken();
      } catch (error) {
        setErrorMessage(error.message);
      }
    },
    [getCreditCardToken, setErrorMessage]
  );

  return (
    <div className="modalForm">
      <form onSubmit={handleOnSubmit}>
        <div className="header">
          <h2>Add Credit Card</h2>
        </div>
        <TextInput placeholder="Card Name" name="fullname" value={fullname} onChange={setFullname} required />
        <div id="spreedly-number" className={classnames('input-container', { focus: isNumberInFocus })} />
        <div id="spreedly-cvv" className={classnames('input-container', { focus: isCvvInFocus })} />
        <ExpiryDateInput placeholder="Expiration (MM/YY)" name="exp-date" month={expiryMonth} fullYear={expiryYear} onChange={updateExpirationDate} />
        <TextInput placeholder="Zip" name="zip" value={zip} onChange={setZip} required />

        {errorMessage ? <div className="errorMessage">{errorMessage}</div> : null}

        <input type="submit" value="SAVE" />
        <button type="button" onClick={cancel}>CANCEL</button>
      </form>
    </div>
  );
}

function getInputStyle() {
  const style = [
    'color: #515150',
    'font-family: \'Roboto Slab Light\'',
    'font-size: 16px',
    'font-weight: 300',
    'line-height: 21px',
    'text-transform: none',
    'cursor: pointer',
  ];
  return style.join(';') + ';';
}
