import map from 'lodash/map';
import cloneDeep from 'lodash/cloneDeep';
import reject from 'lodash/reject';

import React, { useState, useEffect, useCallback } from 'react';
import { LoadingPage, ErrorPage, PaymentMethodComponent, ConfirmModal } from '../Components';
import { LoadingStatus } from '../Common/Types';
import { BillingType } from '../Common/PaymentMethod';
import { usePromise } from '../Utilities/Hooks';
import { getAsset } from '../Utilities/assetsHelper';
import { setIsLoading, setErrorModalMessage } from '../State/Global';
import { getUserBillingAccounts, deleteUserBillingAccount } from '../State/User';
import { OloUser, OloBillingAccount } from '../OloAPI';

export function PaymentContent({ user }: { user: OloUser }) {
  const [currentBillingAccount, setCurrentBillingAccount] = useState<OloBillingAccount | undefined>(undefined);
  const [isConfirmDeleteModalOpen, setIsConfirmDeleteModalOpen] = useState<boolean>(false);
  const billingAccountsStatus = usePromise(async () => getUserBillingAccounts(), [user]);

  const [billingAccounts, setBillingAccounts] = useState<OloBillingAccount[]>([]);

  useEffect(() => {
    if (billingAccountsStatus.status === LoadingStatus.success) {
      setBillingAccounts(cloneDeep(billingAccountsStatus.value));
    }
  }, [billingAccountsStatus, setBillingAccounts]);

  const attemptRemoveBillingAccount = useCallback(
    (newCurrentBillingAccount: OloBillingAccount) => () => {
      setCurrentBillingAccount(newCurrentBillingAccount);
      setIsConfirmDeleteModalOpen(true);
    },
    [setCurrentBillingAccount, setIsConfirmDeleteModalOpen]
  );

  /* Modal */
  const confirmDeleteBillingAccountModal = useCallback(async () => {
    setIsConfirmDeleteModalOpen(false);

    try {
      setIsLoading(true);
      await deleteUserBillingAccount(currentBillingAccount!.accountid);
      setBillingAccounts(reject(billingAccounts, { accountid: currentBillingAccount!.accountid }));
      setIsLoading(false);
    } catch (error) {
      setIsLoading(false);
      setErrorModalMessage(error);
    } finally {
      setCurrentBillingAccount(undefined);
    }
  }, [setCurrentBillingAccount, setIsConfirmDeleteModalOpen, currentBillingAccount, user, billingAccounts, setBillingAccounts]);

  const cancelDeleteBillingAccountModal = useCallback(() => {
    setIsConfirmDeleteModalOpen(false);
    setCurrentBillingAccount(undefined);
  }, [setCurrentBillingAccount, setIsConfirmDeleteModalOpen]);

  if (billingAccountsStatus.status === LoadingStatus.progress) {
    return <LoadingPage />;
  }

  if (billingAccountsStatus.status === LoadingStatus.error) {
    return <ErrorPage error={billingAccountsStatus.error} />;
  }

  return (
    <div className="paymentContent">
      <h1>Payment</h1>

      {!billingAccounts.length ? (
        <strong>You currently don't have a credit card on file.</strong>
      ) : (
        <div className="billingAccounts">
          {map(billingAccounts, billingAccount => {
            return (
              <div key={billingAccount.accountid} className="billingAccount">
                <PaymentMethodComponent
                  paymentMethod={{
                    id: String(billingAccount.accountid),
                    type: BillingType.OloBillingAccount,
                    method: billingAccount,
                  }}
                />
                {billingAccount.removable ? (
                  <button className="remove" onClick={attemptRemoveBillingAccount(billingAccount)}>
                    <img src={getAsset('close.svg')} />
                  </button>
                ) : null}
              </div>
            );
          })}
        </div>
      )}

      <ConfirmModal
        isOpen={isConfirmDeleteModalOpen}
        message={
          currentBillingAccount
            ? `Are you sure you want to delete the ${currentBillingAccount.description}?`
            : 'Are you sure you want to delete this payment method?'
        }
        confirmMessage="delete"
        confirmCallback={confirmDeleteBillingAccountModal}
        cancelCallback={cancelDeleteBillingAccountModal}
      />
    </div>
  );
}
