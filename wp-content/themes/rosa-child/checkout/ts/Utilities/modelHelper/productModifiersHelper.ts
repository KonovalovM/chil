import map from 'lodash/map';
import filter from 'lodash/filter';
import find from 'lodash/find';
import differenceBy from 'lodash/differenceBy';
import sumBy from 'lodash/sumBy';
import some from 'lodash/some';
import flatten from 'lodash/flatten';
import concat from 'lodash/concat';
import reject from 'lodash/reject';
import assign from 'lodash/assign';

import { quantityToString } from '../../Utilities/formatHelper';
import { OloRestaurantMenuProduct, OloRestaurantMenuProductModifier, OloBasketNewProductChoice } from '../../OloAPI';
import { ProductLayout } from '../../Common/Types';

export function addChoiceToSelections(
  product: OloRestaurantMenuProduct,
  choiceId: string,
  oldChoices: OloBasketNewProductChoice[]
): OloBasketNewProductChoice[] {
  const modifier = getProductChoiceModifier(product, choiceId);

  if (modifier.mandatory) {
    return addProductOfMandatoryModifier(product, modifier, choiceId, oldChoices);
  }

  return addProductOfModifier(product, modifier, choiceId, oldChoices);
}

//Add just one product, works as toggle buttons
function addProductOfMandatoryModifier(
  product: OloRestaurantMenuProduct,
  modifier: OloRestaurantMenuProductModifier,
  choiceId: string,
  oldChoices: OloBasketNewProductChoice[]
) {
  if (find(oldChoices, { choiceid: choiceId })) {
    return removeChoiceFromSelections(product, choiceId, oldChoices);
  }

  const newChoice = { choiceid: choiceId, quantity: 1 };
  const selectedChoicesInModifier = getSelectedChoicesInModifier(modifier, oldChoices);
  const choicesWithOutChoicesInModifier = differenceBy(oldChoices, selectedChoicesInModifier, 'choiceid');

  return concat(choicesWithOutChoicesInModifier, [newChoice]);
}

function addProductOfModifier(
  product: OloRestaurantMenuProduct,
  modifier: OloRestaurantMenuProductModifier,
  choiceId: string,
  oldChoices: OloBasketNewProductChoice[]
) {
  const { choicequantityincrement, minchoicequantity, maxchoicequantity } = modifier;

  // Get existing choice or create new choice and update it
  const oldChoice = find(oldChoices, { choiceid: choiceId }) || { choiceid: choiceId, quantity: 0 };
  //if the choice was not found (quanity = 0) the increment should be the minchoicequantity
  //but only if the minchoicequantity > choicequantityincrement
  const increment = !oldChoice.quantity && minchoicequantity > choicequantityincrement ? minchoicequantity : choicequantityincrement;
  const newChoice = assign({}, oldChoice, { quantity: wraparound(oldChoice.quantity + increment, 0, maxchoicequantity) });

  const newChoices = appendNewChoice(modifier, newChoice, oldChoices);
  return newChoices || removeChoiceFromSelections(product, choiceId, oldChoices);
}

function appendNewChoice(modifier: OloRestaurantMenuProductModifier, newChoice: OloBasketNewProductChoice, oldChoices: OloBasketNewProductChoice[]) {
  const { maxselects, maxaggregatequantity } = modifier;

  const newChoices = concat(
    filter(oldChoices, choice => choice.choiceid !== newChoice.choiceid),
    newChoice.quantity ? [newChoice] : []
  );

  // Check num selects
  const choicesInModifier = getSelectedChoicesInModifier(modifier, newChoices);
  if (choicesInModifier.length > maxselects) {
    return undefined;
  }

  // Check aggregate quantity
  const aggregateChoicesInModifier = sumBy(choicesInModifier, 'quantity');
  if (aggregateChoicesInModifier > maxaggregatequantity) {
    return undefined;
  }

  return newChoices;
}

export const wraparound = (num: number, minumum: number, maximum: number) => (num > maximum ? minumum : num);

export function getProductModifier(product: OloRestaurantMenuProduct, modifierId: string) {
  const foundModifier = find(product.modifiers, { id: modifierId });
  if (!foundModifier) {
    throw new Error(`Unable to find modifier ${modifierId} on product ${product.id}!`);
  }
  return foundModifier;
}

export function getProductChoiceModifier(product: OloRestaurantMenuProduct, choiceId: string) {
  const modifier = find(product.modifiers, productModifier => some(productModifier.choices, { id: choiceId }));
  if (!modifier) {
    throw new Error(`Unable to find modifier for choice ${choiceId} on product ${product.id}!`);
  }
  return modifier;
}

export function getProductChoice(product: OloRestaurantMenuProduct, choiceId: string) {
  const modifier = getProductChoiceModifier(product, choiceId);
  const choiceSpec = find(modifier.choices, { id: choiceId });
  if (!choiceSpec) {
    throw new Error(`Unable to find choice ${choiceId} on modifier ${modifier.id} on product ${product.id}!`);
  }
  return choiceSpec;
}

export function removeChoiceFromSelections(
  product: OloRestaurantMenuProduct,
  choiceId: string,
  oldChoices: OloBasketNewProductChoice[]
): OloBasketNewProductChoice[] {
  return reject(oldChoices, { choiceid: choiceId });
}

export function removeSelectedChoicesInModifier(modifier: OloRestaurantMenuProductModifier, selectedChoices: OloBasketNewProductChoice[]) {
  const choicesInModifier = map(getSelectedChoicesInModifier(modifier, selectedChoices), 'choiceid');
  return filter(selectedChoices, selectedChoice => !choicesInModifier.includes(selectedChoice.choiceid));
}

export function getSelectedChoicesInModifier(modifier: OloRestaurantMenuProductModifier, selectedChoices: OloBasketNewProductChoice[]) {
  return filter(selectedChoices, selectedChoice => some(modifier.choices, { id: selectedChoice.choiceid }));
}

export function calculateCaloriesForSelectedChoices(product: OloRestaurantMenuProduct, selectedChoices: OloBasketNewProductChoice[]) {
  return sumBy(selectedChoices, choice => {
    const productChoice = getProductChoice(product, choice.choiceid);
    const calories = typeof productChoice.calories === 'object' ? (productChoice.calories.min + productChoice.calories.max) / 2 : productChoice.calories;
    return calories * choice.quantity;
  });
}

export function calculatePriceForSelectedChoices(product: OloRestaurantMenuProduct, selectedChoices: OloBasketNewProductChoice[]) {
  return product.cost + sumBy(selectedChoices, choice => getProductChoice(product, choice.choiceid).cost * choice.quantity);
}

export function getProductDefaultSelectedChoices(product: OloRestaurantMenuProduct): OloBasketNewProductChoice[] {
  return flatten(
    map(product.modifiers, modifier =>
      map(filter(modifier.choices, 'isdefault'), choice => ({
        choiceid: choice.id,
        quantity: modifier.supportschoicequantities ? (modifier.minchoicequantity || modifier.choicequantityincrement) : 1,
      }))
    )
  );
}

export function isModifierFull(selectedChoicesInModifier: OloBasketNewProductChoice[], maxaggregatequantity: number) {
  const aggregateChoicesInModifier = sumBy(selectedChoicesInModifier, 'quantity');
  return aggregateChoicesInModifier === maxaggregatequantity;
}

export function isParentModifier(modifiers: OloRestaurantMenuProductModifier[], modifier: OloRestaurantMenuProductModifier) {
  if(modifier.parentId) {
    return false;
  }
  return find(modifiers, m => m.parentId === modifier.id) ? true : false;
}

/*The modifier should be hidden when
it is an extra modifier or
it is a parent modifier with only one choice and that choice is set as default
*/
export function shouldHideModifier(modifiers: OloRestaurantMenuProductModifier[], modifier: OloRestaurantMenuProductModifier) {
  if(modifier.extraOf) {
    return true;
  }

  if(modifier.choices.length === 1) {
    const choice = modifier.choices[0];
    return isParentModifier(modifiers, modifier) && choice.isdefault;
  }
  return false;
}

export function getNoHiddeModifier(modifiers:OloRestaurantMenuProductModifier[]) {

  return filter(modifiers, modifier => !shouldHideModifier(modifiers, modifier));
}

export function getSelectedChoiceQuantity(selectedChoicesInModifier: OloBasketNewProductChoice[], choiceid: string, fractional: boolean) {
  const selectedChoice = find(selectedChoicesInModifier, { choiceid }) || { quantity: 0 };
  const denominator = selectedChoicesInModifier.length > 0 ? selectedChoicesInModifier.length : 1;
  const quantity = selectedChoice.quantity;
  return quantityToString(quantity, fractional ? denominator : 1);
}

export function isModifierOptional(modifier: OloRestaurantMenuProductModifier) {
  return modifier.minselects === 0 && modifier.minaggregatequantity === 0 && modifier.minchoicequantity === 0;
}

export function isValidExtraModifier(modifier: OloRestaurantMenuProductModifier) {
  const { extraOf, supportschoicequantities, maxselects, choices } = modifier;

  if (extraOf === undefined && supportschoicequantities && isFinite(maxselects) && !choices.length) {
    return false;
  }

  return true;
}

export function getProductLayout(modifiers: OloRestaurantMenuProductModifier[] | undefined) {
  if (modifiers === undefined) {
    return ProductLayout.simple;
  }

  if (!modifiers.length) {
    return ProductLayout.simple;
  }

  return ProductLayout.complex;
}

export function getUnavailableChoices(product: OloRestaurantMenuProduct): OloBasketNewProductChoice[] {
  const modifiers = product.modifiers || ([] as OloRestaurantMenuProductModifier[]);
  return flatten(
    map(modifiers, modifier => {
      const available = modifier.availability.available;
      const unavailableChoices = !available ? modifier.choices : filter(modifier.choices, choice => !choice.availability.available);
      return map(unavailableChoices, choice => {
        return { choiceid: choice.id, quantity: 0, customfields: [] };
      });
    })
  );
}

function getChoicesTextInModifier(
  modifier: OloRestaurantMenuProductModifier,
  selectedChoices: OloBasketNewProductChoice[],
  parentModifier?: OloRestaurantMenuProductModifier
) {

  const selectedChoicesInModifier = getSelectedChoicesInModifier(modifier, selectedChoices);

  const choicePrefix = parentModifier ? (parentModifier.choices[0].name ? `${parentModifier.choices[0].name}: ` : '') : '';

  const choicesText = map(selectedChoicesInModifier, selectedChoice => {
    const quantity = getSelectedChoiceQuantity(selectedChoicesInModifier, selectedChoice.choiceid, modifier.fractional);
    const choice = find(modifier.choices, { id: selectedChoice.choiceid });
    return choice ? `${choicePrefix}${choice.name} (${quantity})` : '';
  });
  return choicesText;
}

export function getChoicesText(modifiers: OloRestaurantMenuProductModifier[], selectedChoices: OloBasketNewProductChoice[]) {
  const choicesNames = flatten(map(modifiers, modifier => getChoicesTextInModifier(modifier, selectedChoices, find(modifiers, { id: modifier.parentId }))));
  return filter(choicesNames, choiceName => choiceName !== '').join(', ');
}
