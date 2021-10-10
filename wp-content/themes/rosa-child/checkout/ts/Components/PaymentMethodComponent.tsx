import React from 'react';
import { BillingType, PaymentMethod } from '../Common/PaymentMethod';
import { addZeroPadding } from '../Utilities/formatHelper';
import { getAsset } from '../Utilities/assetsHelper';

export const PaymentMethodComponent = ({ paymentMethod }: { paymentMethod: PaymentMethod }) => {
  if (paymentMethod.type === BillingType.OloBillingAccount && paymentMethod.method.accounttype !== 'creditcard') {
    return <div className="paymentMethodComponent">{paymentMethod.method.description}</div>;
  }

  if (paymentMethod.type === BillingType.OloBillingAccount) {
    const billingAccount = paymentMethod.method;

    const cardtype = billingAccount.cardtype;
    const icon = cardtype ? getCardIcon(cardtype.toLowerCase()) : undefined;

    const cardsuffix = billingAccount.cardsuffix!;
    const expiration = billingAccount.expiration!.split('-');

    const expiryYear = Number(expiration[0]);
    const expiryMonth = Number(expiration[1]);

    return <CreditCardComponent {...{ icon, cardsuffix, expiryYear, expiryMonth }} />;
  } else {
    const creditcard = paymentMethod.method;

    const cardtype = creditcard.cardtype;
    const icon = getCardIcon(cardtype.toLowerCase());

    const cardsuffix = creditcard.sufix;

    const expiryMonth = creditcard.month;
    const expiryYear = creditcard.year;

    return <CreditCardComponent {...{ icon, cardsuffix, expiryYear, expiryMonth }} />;
  }
};

function CreditCardComponent({ icon, cardsuffix, expiryMonth, expiryYear }: { icon?: string; cardsuffix: string; expiryMonth: number; expiryYear: number }) {
  return (
    <div className="paymentMethodComponent">
      {icon ? <img src={icon} /> : null}

      <div className="info">
        <small>last four</small>
        <strong>{`•••• ${cardsuffix}`}</strong>
      </div>

      <div className="info">
        <small>expiration</small>
        <strong>{`${addZeroPadding(Number(expiryMonth))}/${expiryYear}`}</strong>
      </div>
    </div>
  );
}

function getCardIcon(cardtype: string) {
  if (cardtype === 'amex') {
    return getAsset('amex.png');
  } else if (cardtype === 'visa') {
    return getAsset('visa.png');
  } else if (cardtype === 'discover') {
    return getAsset('discover.png');
  } else if (cardtype === 'mastercard') {
    return getAsset('mastercard.png');
  } else {
    return undefined;
  }
}
