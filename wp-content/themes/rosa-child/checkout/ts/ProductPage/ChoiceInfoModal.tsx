import React from 'react';
import Modal from 'react-modal';
import flatten from 'lodash/flatten';
import map from 'lodash/map';
import find from 'lodash/find';
import { OloRestaurantMenuProduct } from '../OloAPI';
import { getAsset } from '../Utilities/assetsHelper';
import { useModalValues } from '../Utilities/Hooks';

export const ChoiceInfoModal = ({ product, choiceId, close }: { product: OloRestaurantMenuProduct; choiceId?: string; close: () => unknown }) => {
  const [isOpen, usedChoiceId] = useModalValues<string | undefined>(choiceId);

  return (
    <Modal
      isOpen={isOpen}
      overlayClassName="modalOverlay"
      className="centeredContent"
      closeTimeoutMS={200}
      shouldCloseOnOverlayClick={true}
      onRequestClose={close}
    >
      <ChoiceInfoModalContent product={product} choiceId={usedChoiceId} close={close} />
    </Modal>
  );
};

const ChoiceInfoModalContent = ({ product, choiceId, close }: { product: OloRestaurantMenuProduct; choiceId: string | undefined; close: () => unknown }) => {
  if (!choiceId) {
    return null;
  }

  const { modifiers } = product;
  if (!modifiers) {
    return (
      <div className="choiceInfoModal">
        <div className="errorMessage">*Error: The product does not have modifiers</div>
      </div>
    );
  }

  const choices = flatten(map(modifiers, 'choices'));
  const choice = find(choices, { id: choiceId });
  if (!choice) {
    return (
      <div className="choiceInfoModal">
        <div className="errorMessage">*Error: Choice not found</div>
      </div>
    );
  }

  return (
    <div className="choiceInfoModal">
      <button onClick={close}>
        <img src={getAsset('close-light.svg')} />
      </button>
      <div className="imgContainer">
        <div className="imageCover" />
        <img src={choice.img} />
      </div>
      <div className="choice">
        <h2>{choice.name}</h2>
        <small>{choice.calories + ' Cals'}</small>
        <p>{choice.description}</p>
      </div>
    </div>
  );
};
