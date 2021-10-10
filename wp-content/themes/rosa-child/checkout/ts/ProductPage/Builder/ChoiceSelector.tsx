import React, { useCallback } from 'react';
import { OloRestaurantMenuProductModifierChoice } from '../../OloAPI';
import classnames from 'classnames';
import { getCostString } from '../../Utilities/formatHelper';
import { getAsset } from '../../Utilities/assetsHelper';

export interface ChoiceSelectorProps {
  choice: OloRestaurantMenuProductModifierChoice;
  quantity: string;
  mandatory: boolean;
  addChoice: (choiceId: string) => unknown;
  openChoiceInfo: (modifierId: string) => unknown;
}

export const ChoiceSelector = ({ choice, quantity, mandatory, addChoice, openChoiceInfo }: ChoiceSelectorProps) => {
  const addChoiceHandler = useCallback(() => addChoice(choice.id), [addChoice, choice.id]);
  const openChoiceInfoHandler = useCallback(() => openChoiceInfo(choice.id), [openChoiceInfo, choice.id]);

  const available = choice.availability.available;
  return (
    <div className={classnames('choice', { notAvailable: !available })}>
      <button onClick={available ? addChoiceHandler : undefined}>
        {mandatory ? <Check img={choice.img} quantity={quantity} /> : <Quantity img={choice.img} quantity={quantity} />}
      </button>
      <button onClick={available ? openChoiceInfoHandler : undefined}>
        <h3>{choice.name}</h3>
        <strong>{choice.cost ? '+ ' + getCostString(choice.cost) : null}</strong>
      </button>
    </div>
  );
};

function Quantity({ img, quantity }: { img: string; quantity: string }) {
  return (
    <div className="choiceImg">
      <img src={img} />
      <div className={classnames('quantity', { visible: quantity.length > 0 })}>{quantity}</div>
    </div>
  );
}

function Check({ img, quantity }: { img: string; quantity: string }) {
  return (
    <div className="choiceImg">
      <img src={img} />
      <img className={classnames('check', { visible: quantity.length > 0 })} src={getAsset('check.svg')} />
    </div>
  );
}
