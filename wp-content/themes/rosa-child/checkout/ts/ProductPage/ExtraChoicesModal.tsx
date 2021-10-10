import React, { useCallback } from 'react';
import Modal from 'react-modal';
import find from 'lodash/find';
import some from 'lodash/some';
import map from 'lodash/map';
import memoize from 'lodash/memoize';
import classnames from 'classnames';
import { OloRestaurantMenuProduct, OloRestaurantMenuProductModifierChoice, OloBasketNewProductChoice } from '../OloAPI';
import { getSelectedChoicesInModifier } from '../Utilities/modelHelper';
import { getCostString } from '../Utilities/formatHelper';
import { getAsset } from '../Utilities/assetsHelper';
import { useModalValues } from '../Utilities/Hooks';

export interface ExtraChoicesModalProps {
  product: OloRestaurantMenuProduct;
  selectedChoices: OloBasketNewProductChoice[];
  modifierId?: string;
  close: () => unknown;
  addChoice: (choiceId: string) => unknown;
}

export const ExtraChoicesModal = ({ product, selectedChoices, modifierId, close, addChoice }: ExtraChoicesModalProps) => {
  const [isOpen, usedModifierId] = useModalValues<string | undefined>(modifierId);

  return (
    <Modal
      isOpen={isOpen}
      overlayClassName="modalOverlay"
      className="centeredContent"
      closeTimeoutMS={200}
      shouldCloseOnOverlayClick={true}
      onRequestClose={close}
    >
      <ExtraChoicesModalContent product={product} selectedChoices={selectedChoices} modifierId={usedModifierId} close={close} addChoice={addChoice} />
    </Modal>
  );
};

export const ExtraChoicesModalContent = ({ product, selectedChoices, modifierId, close, addChoice }: ExtraChoicesModalProps) => {
  const addSingleChoice = useCallback(
    memoize((choiceId: string) => () => addChoice(choiceId)),
    [addChoice]
  );

  if (!modifierId) {
    return null;
  }

  const modifiers = product.modifiers;
  if (!modifiers) {
    return <div className="errorMessage">*Error: the product does not have modifiers</div>;
  }

  const modifier = find(modifiers, { id: modifierId });
  if (!modifier) {
    return <div className="errorMessage">*Error: extra modifier not found</div>;
  }

  const choices = modifier.choices;
  const extraSelectedChoices = getSelectedChoicesInModifier(modifier, selectedChoices);

  return (
    <div className="extraChoicesModal">
      <div className="header">
        <h2>{'Add ' + modifier.description}</h2>
        <button onClick={close}>
          <img src={getAsset('close.svg')} />
        </button>
      </div>
      <div className="choices">
        {map(choices, (choice: OloRestaurantMenuProductModifierChoice) => {
          return <ChoiceComponent key={choice.id} choice={choice} selectedChoices={selectedChoices} addChoice={addSingleChoice(choice.id)} />;
        })}
      </div>
      <div className="saveButtonContainer">
        <button onClick={close} className={classnames('saveButton', { able: extraSelectedChoices.length > 0 })}>
          SAVE
        </button>
      </div>
    </div>
  );
};

const ChoiceComponent = (props: { choice: OloRestaurantMenuProductModifierChoice; selectedChoices: OloBasketNewProductChoice[]; addChoice: () => unknown }) => {
  const { choice, selectedChoices, addChoice } = props;
  const isSelected = some(selectedChoices, { choiceid: choice.id });
  const price = getCostString(choice.cost);
  const addImageSrc = getAsset('increase.svg');
  const removeImageSrc = getAsset('decrease.svg');

  return (
    <div className={classnames('choice', { selected: isSelected })}>
      <button onClick={addChoice}>
        <img src={isSelected ? removeImageSrc : addImageSrc} />
      </button>
      <div className="info">
        <h3>{choice.name}</h3>
        <small>{price}</small>
      </div>
    </div>
  );
};
