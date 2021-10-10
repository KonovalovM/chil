import map from 'lodash/map';
import memoize from 'lodash/memoize';
import assign from 'lodash/assign';
import find from 'lodash/find';
import classnames from 'classnames';
import React, { useState, useEffect, useCallback } from 'react';
import Modal from 'react-modal';

import { OloDeliveryAddress } from '../OloAPI';
import { DeliveryModePayload } from '../Common/DeliveryModePayload';
import { LoadingStatus } from '../Common/Types';
import { usePromise } from '../Utilities/Hooks';
import { getAsset } from '../Utilities/assetsHelper';
import { getDeliveryAddresses, getContactNumber } from '../State/User';
import { DeliveryAddressComponent } from '../Components';
import { TextInput } from '../Components/FormInputs/TextInput';
import { PhoneNumberInput } from '../Components/FormInputs/PhoneNumberInput';

interface DeliveryAddressFieldsProps {
	isGuestUser: boolean;
	deliveryModePayload: DeliveryModePayload;
	updateDeliveryModePayload: (deliveryModePayload: DeliveryModePayload) => void;
  showValidationErrors: boolean
}

export function DeliveryAddressFields(props: DeliveryAddressFieldsProps) {
	const { isGuestUser, deliveryModePayload, updateDeliveryModePayload, showValidationErrors } = props;

  const deliveryAddressesStatus = usePromise(async () => isGuestUser ? [] as OloDeliveryAddress[] : getDeliveryAddresses(), [isGuestUser]);
  const contactNumberStatus = usePromise(async () => isGuestUser ? undefined : getContactNumber(), [isGuestUser]);

	if(deliveryAddressesStatus.status === LoadingStatus.progress && contactNumberStatus.status !== LoadingStatus.success) {
		return (
			<div className="deliveryAddressFields">
      	<div>Loading your Delivery Addresses...</div>
    	</div>
    );
	}

  let deliveryAddresses = [] as OloDeliveryAddress[];
  let phonenumber = contactNumberStatus.status === LoadingStatus.success ? contactNumberStatus.value : undefined;
  let message = null;

  if(deliveryAddressesStatus.status === LoadingStatus.error) {
    message = "Can't load your saved Delivery Addresses";
  }

  if(deliveryAddressesStatus.status === LoadingStatus.success) {
    deliveryAddresses = deliveryAddressesStatus.value;
    message = isGuestUser ? null : ( deliveryAddresses.length > 0 ? "Choose a delivery address" : "Currently you don't have any delivery address saved");
  }

  return (
  	<DeliveryAddressSelectorWithData
  		isGuestUser={isGuestUser}
      deliveryAddresses = {deliveryAddresses}
  		deliveryModePayload = {deliveryModePayload}
      updateDeliveryModePayload = {updateDeliveryModePayload}
      initialPhoneNumber = {phonenumber}
      message={message}
      showValidationErrors={showValidationErrors}
  	/>
  );
}

interface DeliveryAddressSelectorWithDataProps {
	isGuestUser: boolean;
  deliveryAddresses: OloDeliveryAddress[];
	deliveryModePayload: DeliveryModePayload;
  updateDeliveryModePayload: (deliveryModePayload: DeliveryModePayload) => void;
  initialPhoneNumber?: string;
  message: string|null;
  showValidationErrors: boolean;
}

function DeliveryAddressSelectorWithData(props: DeliveryAddressSelectorWithDataProps) {
	const { isGuestUser, deliveryAddresses, deliveryModePayload, updateDeliveryModePayload, initialPhoneNumber, message, showValidationErrors } = props;

  const [ newDeliveryAddress, setNewDeliveryAddress ] = useState<OloDeliveryAddress|undefined>();

  useEffect(() => {
    const selectedDeliveryAddress = deliveryModePayload.type === 'dispatch' ? deliveryModePayload.data : undefined;
    if(!selectedDeliveryAddress) {
      setNewDeliveryAddress(undefined);
      return;
    }
    if(find(deliveryAddresses, { id: selectedDeliveryAddress.id })) {
      setNewDeliveryAddress(undefined);
      return;
    }

    setNewDeliveryAddress(selectedDeliveryAddress);
  }, [deliveryAddresses, deliveryModePayload]);

  const [ isOpenDeliveryAddressModal, setIsOpenDeliveryAddressModal ] = useState<boolean>(false);

	const updateDeliveryAddressHandler = useCallback(
    memoize((deliveryAddress: OloDeliveryAddress) => () => updateDeliveryModePayload({
      type: 'dispatch',
      data: deliveryAddress
    })),
    [updateDeliveryModePayload]
  );

	/*Modal new Delivery Address Form*/
  const openDeliveryAddressForm = useCallback(
    (event: React.MouseEvent) => {
      event.stopPropagation();
      setIsOpenDeliveryAddressModal(true);
    },
    [setIsOpenDeliveryAddressModal]
  );

  const closeDeliveryAddressForm = useCallback(() => {
    setIsOpenDeliveryAddressModal(false);
  }, [setIsOpenDeliveryAddressModal]);

  const updateNewDeliveryAddress = useCallback(
    (deliveryAddress: OloDeliveryAddress) => {
      setNewDeliveryAddress(deliveryAddress);
      updateDeliveryModePayload({
        type: 'dispatch',
        data: deliveryAddress
      });
      setIsOpenDeliveryAddressModal(false);
    },
    [setNewDeliveryAddress, updateDeliveryModePayload, setIsOpenDeliveryAddressModal]
  );

  const isSelected = useCallback((deliveryAddressId: string) => {
    const selectedDeliveryAddress = deliveryModePayload.type === 'dispatch' ? deliveryModePayload.data : undefined;
    if(!selectedDeliveryAddress) {
      return false;
    }
    return selectedDeliveryAddress.id === deliveryAddressId;
  }, [deliveryModePayload]);

	return (
		<>
			<div className="deliveryAddressFields">
				{message ? <h3>{message}</h3> : null}
				{showValidationErrors && deliveryModePayload.data === undefined ? <div className="errorMessage">*Select a saved delivery address or add a new one.</div> : null}
        <div className="deliveryAddresses">
					{map(deliveryAddresses, deliveryAddress => (
						<div
              key={deliveryAddress.id}
              onClick={updateDeliveryAddressHandler(deliveryAddress)}
              className={classnames('deliveryAddressContainer', { selected: isSelected(deliveryAddress.id) })}
            >
              <DeliveryAddressComponent deliveryAddress={deliveryAddress} />
            </div>
					))}

					{newDeliveryAddress ? (
            <div
              onClick={updateDeliveryAddressHandler(newDeliveryAddress)}
              className={classnames('deliveryAddressContainer', { selected: isSelected(newDeliveryAddress.id) })}
            >
              <DeliveryAddressComponent deliveryAddress={newDeliveryAddress} />
              <button type="button" className="edit" onClick={openDeliveryAddressForm}>
                <img src={getAsset('edit.svg')} />
              </button>
            </div>
          ) : (
            <button type="button" className="addDeliveryAddressButton" onClick={openDeliveryAddressForm}>
              Add New Delivery Address
            </button>
          )}
				</div>
			</div>
			<Modal
        isOpen={isOpenDeliveryAddressModal}
        overlayClassName="modalOverlay"
        className="centeredContent"
        closeTimeoutMS={200}
        shouldCloseOnOverlayClick={true}
        onRequestClose={closeDeliveryAddressForm}
      >
        <DeliveryAddressForm
          isGuestUser={isGuestUser}
          deliveryAddress={newDeliveryAddress}
          initialPhoneNumber={initialPhoneNumber}
          cancel={closeDeliveryAddressForm}
          onSubmit={updateNewDeliveryAddress}
        />
      </Modal>
		</>
	);
}

interface DeliveryAddressFormProps {
  isGuestUser: boolean;
  deliveryAddress: OloDeliveryAddress | undefined;
  initialPhoneNumber?: string
  cancel: () => void;
  onSubmit: (deliveryAddress: OloDeliveryAddress) => void;
}

export function DeliveryAddressForm(props: DeliveryAddressFormProps) {
  const { isGuestUser, deliveryAddress, initialPhoneNumber, cancel, onSubmit } = props;

  const [ streetaddress, setStreetaddress ] = useState<string>(deliveryAddress ? deliveryAddress.streetaddress : '');
  const [ city, setCity ] = useState<string>(deliveryAddress ? deliveryAddress.city : '');
  const [ zipcode, setZipcode ] = useState<string>(deliveryAddress ? deliveryAddress.zipcode : '');
  const [ phonenumber, setPhonenumber ] = useState<string>(() => {
    if(deliveryAddress && deliveryAddress.phonenumber) {
      return deliveryAddress.phonenumber;
    }
    return initialPhoneNumber ? initialPhoneNumber: '';
  });
  const [ building, setBuilding ] = useState<string>(deliveryAddress && deliveryAddress.building ? deliveryAddress.building : '');
  const [ specialinstructions, setSpecialinstructions] = useState<string>(deliveryAddress && deliveryAddress.specialinstructions ? deliveryAddress.specialinstructions : '');

  const updateSpecialinstructions = useCallback((event: React.ChangeEvent<HTMLTextAreaElement>) => {
    setSpecialinstructions(event.target.value);
  }, [setSpecialinstructions]);

  const handleOnSubmit = useCallback((event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    event.stopPropagation();

    const deliveryAddressesData = {
      id: '',
      streetaddress,
      city,
      zipcode,
      phonenumber,
      building,
      isdefault: false,
      specialinstructions
    };

    if(!isGuestUser) {
      assign(deliveryAddressesData, { phonenumber })
    }

    onSubmit(deliveryAddressesData);

  }, [streetaddress, city, zipcode, phonenumber, building, specialinstructions, isGuestUser, onSubmit ]);

  return (
    <div className="modalForm">
      <form onSubmit={handleOnSubmit}>
        <div className="header">
          <h2>Add Delivery Address</h2>
        </div>
        <TextInput placeholder="Street Address" name="streetaddress" value={streetaddress} onChange={setStreetaddress} required />
        <TextInput placeholder="City" name="city" value={city} onChange={setCity} required />
        <TextInput placeholder="Zip" name="zip" value={zipcode} onChange={setZipcode} required />
        {isGuestUser ? null : <PhoneNumberInput phoneNumber={phonenumber} onChange={setPhonenumber} required/>}
        <TextInput placeholder="Building (optional)" name="building" value={building} onChange={setBuilding} />
        <textarea placeholder="Special Instructions" value={specialinstructions}  onChange={updateSpecialinstructions} />

        <input type="submit" value="SAVE" />
        <button type="button" onClick={cancel}>CANCEL</button>
      </form>
    </div>
  );
}