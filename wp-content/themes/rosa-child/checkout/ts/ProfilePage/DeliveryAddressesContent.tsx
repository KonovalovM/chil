import map from 'lodash/map';
import cloneDeep from 'lodash/cloneDeep';
import reject from 'lodash/reject';
import React, { useState, useEffect, useCallback } from 'react';
import { LoadingPage, ErrorPage, DeliveryAddressComponent, ConfirmModal } from '../Components';
import { LoadingStatus } from '../Common/Types';
import { OloUser, OloDeliveryAddress } from '../OloAPI';
import { getDeliveryAddresses, deleteDeliveryAddress } from '../State/User';
import { setIsLoading, setErrorModalMessage } from '../State/Global';
import { usePromise } from '../Utilities/Hooks';
import { getAsset } from '../Utilities/assetsHelper';

export function DeliveryAddressesContent({ user }: { user: OloUser }) {
	const [currentDeliveryAddress, setCurrentDeliveryAddress] = useState<OloDeliveryAddress | undefined>(undefined);
	const [isConfirmDeleteModalOpen, setIsConfirmDeleteModalOpen] = useState<boolean>(false);
	const deliveryAddressesStatus = usePromise(async () => getDeliveryAddresses(), [user]);

	const [deliveryAddresses, setDeliveryAddresses] = useState<OloDeliveryAddress[]>([]);

	 useEffect(() => {
    if (deliveryAddressesStatus.status === LoadingStatus.success) {
      setDeliveryAddresses(cloneDeep(deliveryAddressesStatus.value));
    }
  }, [deliveryAddressesStatus, setDeliveryAddresses]);

	const attemptRemoveDeliveryAddress = useCallback(
    (newCurrentDeliveryAddress: OloDeliveryAddress) => () => {
      setCurrentDeliveryAddress(newCurrentDeliveryAddress);
      setIsConfirmDeleteModalOpen(true);
    },
    [setCurrentDeliveryAddress, setIsConfirmDeleteModalOpen]
  );

	/* Modal */
  const confirmDeleteDeliveryAddressModal = useCallback(async () => {
    setIsConfirmDeleteModalOpen(false);

    try {
      setIsLoading(true);
      await deleteDeliveryAddress(currentDeliveryAddress!.id);
      setDeliveryAddresses(reject(deliveryAddresses, { id: currentDeliveryAddress!.id }));
      setIsLoading(false);
    } catch (error) {
      setIsLoading(false);
      setErrorModalMessage(error);
    } finally {
      setCurrentDeliveryAddress(undefined);
    }
  }, [setCurrentDeliveryAddress, setIsConfirmDeleteModalOpen, currentDeliveryAddress, user, deliveryAddresses, setDeliveryAddresses]);

  const cancelDeleteDeliveryAddressModal = useCallback(() => {
    setIsConfirmDeleteModalOpen(false);
    setCurrentDeliveryAddress(undefined);
  }, [setCurrentDeliveryAddress, setIsConfirmDeleteModalOpen]);


	if (deliveryAddressesStatus.status === LoadingStatus.progress) {
    return <LoadingPage />;
  }

  if (deliveryAddressesStatus.status === LoadingStatus.error) {
    return <ErrorPage error={deliveryAddressesStatus.error} />;
  }

	return (
    <div className="deliveryAddressesContent">
      <h1>Delivery Addresses</h1>

      {!deliveryAddresses.length ? (
        <strong>You currently don't have a saved delivery address.</strong>
      ) : (
        <div className="deliveryAddresses">
          {map(deliveryAddresses, deliveryAddress => {
            return (
              <div key={deliveryAddress.id} className="deliveryAddress">
                <DeliveryAddressComponent
                  deliveryAddress={deliveryAddress}
                />
                <button className="remove" onClick={attemptRemoveDeliveryAddress(deliveryAddress)}>
                  <img src={getAsset('close.svg')} />
                </button>
              </div>
            );
          })}
        </div>
      )}

      <ConfirmModal
        isOpen={isConfirmDeleteModalOpen}
        message={'Are you sure you want to delete this delivery address?'}
        confirmMessage="delete"
        confirmCallback={confirmDeleteDeliveryAddressModal}
        cancelCallback={cancelDeleteDeliveryAddressModal}
      />
    </div>
  );
}