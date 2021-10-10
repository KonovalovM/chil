import map from 'lodash/map';
import memoize from 'lodash/memoize';
import classnames from 'classnames';
import React, { useState, useCallback } from 'react';
import Modal from 'react-modal';

import { getUserBillingAccounts } from '../State/User';

import { LoadingStatus } from '../Common/Types';
import { BillingType, CreditCard, PaymentMethod } from '../Common/PaymentMethod';
import { getAsset } from '../Utilities/assetsHelper';
import { usePromise } from '../Utilities/Hooks';
import { PaymentMethodComponent } from '../Components';

import { CreditCardForm } from './CreditCardForm';

export const PaymentMethodSelector = ({
  isGuestUser,
  selectedPaymentMethod,
  updatePaymentMethod,
  showValidationErrors
}: {
  isGuestUser: boolean;
  selectedPaymentMethod: PaymentMethod | undefined;
  updatePaymentMethod: (paymentMehtod: PaymentMethod) => void;
  showValidationErrors: boolean;
}) => {
  const billingAccountsStatus = usePromise(async () => isGuestUser ? [] : getUserBillingAccounts(), [isGuestUser]);

  if (billingAccountsStatus.status === LoadingStatus.progress) {
    return (
      <div className="paymentMethodSelector">
        <div>Loading your Payment methods...</div>
      </div>
    );
  }

  let message = null;
  let paymentMethods = [] as PaymentMethod[];

  if(billingAccountsStatus.status === LoadingStatus.error) {
    message = "Can't load your saved Payment methods";
  }

  if(billingAccountsStatus.status === LoadingStatus.success) {
    paymentMethods = map(billingAccountsStatus.value, billingAccount => {
      return {
        id: billingAccount.accountid,
        type: BillingType.OloBillingAccount,
        method: billingAccount,
      };
    });

    message = isGuestUser ? null : ( paymentMethods.length > 0 ? "Choose a payment method" : "Currently you don't have any payment method saved");
  }

  return (
    <PaymentMethodSelectorWithData
      paymentMethods={paymentMethods}
      selectedPaymentMethod={selectedPaymentMethod}
      updatePaymentMethod={updatePaymentMethod}
      message={message}
      showValidationErrors={showValidationErrors}
    />
  );
};

const PaymentMethodSelectorWithData = ({
  paymentMethods,
  selectedPaymentMethod,
  updatePaymentMethod,
  message,
  showValidationErrors
}: {
  paymentMethods: PaymentMethod[],
  selectedPaymentMethod: PaymentMethod | undefined,
  updatePaymentMethod: (paymentMehtod: PaymentMethod) => void,
  message: string| null,
  showValidationErrors: boolean
}) => {
  const [isOpenCreditCardModal, setIsOpenCreditCardModal] = useState<boolean>(false);

  const [creditCard, setCreditCard] = useState<PaymentMethod | undefined>(undefined);
  const updatePaymentMethodHandler = useCallback(
    memoize((newPaymentMethod: PaymentMethod) => () => updatePaymentMethod(newPaymentMethod)),
    [updatePaymentMethod]
  );

  /*Modal Credit Card Form*/
  const openCreditCardForm = useCallback(
    (event: React.MouseEvent) => {
      event.stopPropagation();
      setIsOpenCreditCardModal(true);
    },
    [setIsOpenCreditCardModal]
  );

  const closeCreditCardForm = useCallback(() => {
    setIsOpenCreditCardModal(false);
  }, [setIsOpenCreditCardModal]);

  const updateCreditCard = useCallback(
    (newCreditCard: CreditCard) => {
      setCreditCard({ id: newCreditCard.token, type: BillingType.CreditCard, method: newCreditCard });
      updatePaymentMethod({ id: newCreditCard.token, type: BillingType.CreditCard, method: newCreditCard });
      setIsOpenCreditCardModal(false);
    },
    [updatePaymentMethod, setCreditCard, setIsOpenCreditCardModal]
  );

  return (
    <>
      <div className="paymentMethodSelector">
        {message ? <h3>{message}</h3> : null}
        {showValidationErrors && selectedPaymentMethod === undefined ? <div className="errorMessage">*Select a saved billing account or add a new credit card.</div>: null}
        <div className="paymentMethods">
          {map(paymentMethods, paymentMethod => (
            <div
              key={paymentMethod.id}
              onClick={updatePaymentMethodHandler(paymentMethod)}
              className={classnames('paymentMethodContainer', { selected: selectedPaymentMethod ? selectedPaymentMethod.id === paymentMethod.id : false })}
            >
              <PaymentMethodComponent paymentMethod={paymentMethod} />
            </div>
          ))}

          {creditCard ? (
            <div
              onClick={updatePaymentMethodHandler(creditCard)}
              className={classnames('paymentMethodContainer', { selected: selectedPaymentMethod ? selectedPaymentMethod.id === creditCard.id : false })}
            >
              <PaymentMethodComponent paymentMethod={creditCard} />
              <button type="button" className="edit" onClick={openCreditCardForm}>
                <img src={getAsset('edit.svg')} />
              </button>
            </div>
          ) : (
            <button type="button" className="addCreditCardButton" onClick={openCreditCardForm}>
              Add new credit card
            </button>
          )}
        </div>
      </div>

      <Modal
        isOpen={isOpenCreditCardModal}
        overlayClassName="modalOverlay"
        className="centeredContent"
        closeTimeoutMS={200}
        shouldCloseOnOverlayClick={true}
        onRequestClose={closeCreditCardForm}
      >
        <CreditCardForm creditcard={creditCard ? (creditCard.method as CreditCard) : undefined} cancel={closeCreditCardForm} onSubmit={updateCreditCard} />
      </Modal>
    </>
  );
};
