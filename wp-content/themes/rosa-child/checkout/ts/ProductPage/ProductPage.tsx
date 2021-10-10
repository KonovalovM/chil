import debounce from 'lodash/debounce';
import find from 'lodash/find';
import some from 'lodash/some';
import flatten from 'lodash/flatten';
import uniq from 'lodash/uniq';
import sumBy from 'lodash/sumBy';
import cloneDeep from 'lodash/cloneDeep';
import filter from 'lodash/filter';
import concat from 'lodash/concat';
import map from 'lodash/map';
import without from 'lodash/without';
import fromPairs from 'lodash/fromPairs';
import differenceBy from 'lodash/differenceBy';
import difference from 'lodash/difference';
import omit from 'lodash/omit';

import React, { useCallback, useState, useEffect } from 'react';
import { useHistory, RouteComponentProps } from 'react-router-dom';

import {
  OloRestaurant,
  OloRestaurantMenuProduct,
  OloRestaurantMenuProductModifier,
  OloBasketNewProductChoice,
  OloBasket,
  OloBasketProduct,
  getRestaurantMenuProductWithModifiers,
} from '../OloAPI';

import { RestaurantValidator } from '../Components';
import { makeWithPromiseWithProps } from '../Utilities/componentHelper';
import {
  getProductChoiceModifier,
  getProductModifier,
  addChoiceToSelections,
  removeChoiceFromSelections,
  getSelectedChoicesInModifier,
  removeSelectedChoicesInModifier,
  getProductDefaultSelectedChoices,
  isModifierOptional,
  getUnavailableChoices,
} from '../Utilities/modelHelper';

import { withRestaurant } from '../State/CurrentRestaurant';
import { getAuthtoken } from '../State/User';
import { setIsLoading, setErrorModalMessage } from '../State/Global';
import { withBasket, addProduct, editProduct, openBasket, createBasketNewProduct } from '../State/Basket';

import { ExtraChoicesModal } from './ExtraChoicesModal';
import { ChoiceInfoModal } from './ChoiceInfoModal';
import { ProductBuilder } from './Builder/ProductBuilder';
import { Bag } from './Bag/Bag';
import { AddToBasketButton } from './Bag/AddToBasketButton';
import { Mode } from './Types';

interface ProductPageProps extends RouteComponentProps<{ productId: string; productBasketId?: string | undefined }> {
  restaurant: OloRestaurant;
  basket: OloBasket | undefined;
  product: OloRestaurantMenuProduct;
}

export interface ProductPageErrors {
  modifiers: { [modifierId: string]: string };
  scrollToModifierIdWithError?: string;
}

const withProduct = makeWithPromiseWithProps(
  'product',
  (props: ProductPageProps) => async () => {
    const restaurant = props.restaurant;
    const basket = props.basket;

    const params = props.match.params;

    const productId = params.productId;
    const productBasketId = params.productBasketId;

    const product = await getRestaurantMenuProductWithModifiers(restaurant.id, productId);
    if (!product.availability.available) {
      throw Error(`This product is not available: ${product.availability.description}`);
    }
    if (!product.modifiers || !product.modifiers.length) {
      throw new Error('The product is not customizable');
    }
    if (productBasketId && !basket) {
      throw new Error('Your are trying to change a product from a basket that was not found');
    }
    return product;
  },
  (props: ProductPageProps) => [props.restaurant.id, props.match.params.productId, props.match.params.productBasketId]
);

export const ProductPage = withBasket(
  withRestaurant(
    withProduct((props: ProductPageProps) => {
      const { restaurant, basket, product } = props;

      const params = props.match.params;
      const productBasketId = params.productBasketId;

      const [modifierExtraChoicesId, setModifierExtraChoicesId] = useState<string | undefined>(undefined);
      const [choiceInfoId, setChoiceInfoId] = useState<string | undefined>(undefined);

      const [openMobileBag, setOpenMobileBag] = useState(false);
      const [errors, setErrors] = useState<ProductPageErrors>({ modifiers: {} });

      const [skippedModifiers, setSkippedModifiers] = useState<string[]>([]);
      const [selectedChoices, setSelectedChoices] = useState<OloBasketNewProductChoice[]>([]);
      const [specialinstructions, setSpecialinstructions] = useState<string>('');

      const history = useHistory();

      /* Initializaton of variables */
      useEffect(() => {
        try {
          const basketProduct = productBasketId && basket ? getProductFromBasket(basket, productBasketId) : undefined;
          const selectedChoicesFromBasket = basketProduct && basket ? getSelectedChoicesFromBasket(basket, basketProduct) : [];
          const newSelectedChoicesWithDefaults = basketProduct ? selectedChoicesFromBasket : getProductDefaultSelectedChoices(product);

          const newSelectedChoices = differenceBy(newSelectedChoicesWithDefaults, getUnavailableChoices(product), 'choiceid');
          const newSkippedModifiers = basketProduct && basket ? getSkippedModifiersFromBasket(product, basket, basketProduct) : getSkippedModifiers(product, newSelectedChoicesWithDefaults);
          const newSpecialinstructions = basketProduct ? basketProduct.specialinstructions : '';

          setSelectedChoices(newSelectedChoices);
          setSkippedModifiers(newSkippedModifiers);
          setSpecialinstructions(newSpecialinstructions);
        } catch (error) {
          setErrorModalMessage(error);
        }

        //clean up
        return () => {
          setModifierExtraChoicesId(undefined);
          setChoiceInfoId(undefined);
          setOpenMobileBag(false);
          setErrors({ modifiers: {} });
        };
      }, [restaurant, product, basket, productBasketId, setModifierExtraChoicesId, setChoiceInfoId, setOpenMobileBag, setErrors]);

      useEffect(() => window.scrollTo(0, 0), [restaurant, product, basket, productBasketId]);

      /* Mobile Bag View */

      const toggleMobileBag = useCallback(() => setOpenMobileBag(!openMobileBag), [openMobileBag, setOpenMobileBag]);

      const handleResize = useCallback(
        () =>
          debounce(() => {
            const windowWidth = window.innerWidth;
            const mobileBreakPoint = 768;
            if (windowWidth > mobileBreakPoint) {
              setOpenMobileBag(false);
            }
          }),
        [setOpenMobileBag]
      );

      useEffect(() => {
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
      }, [handleResize]);

      /* Error Management */
      const setModifierError = useCallback(
        (modifierId: string, error: string | undefined) => {
          const newErrors = cloneDeep(errors);
          newErrors.scrollToModifierIdWithError = undefined;

          if (!error) {
            delete newErrors.modifiers[modifierId];
          } else {
            newErrors.modifiers[modifierId] = error;
          }
          setErrors(newErrors);
        },
        [errors, setErrors]
      );

      const validateProduct = useCallback(() => {
        const currentProductErrors = getProductErrors(product, selectedChoices, skippedModifiers);
        const modifiersErrors = fromPairs(currentProductErrors);
        const modifiersIdWithError = Object.keys(modifiersErrors);
        const scrollToModifierIdWithError = modifiersIdWithError.length > 0 ? modifiersIdWithError[0] : undefined;

        const newErrors: ProductPageErrors = {
          modifiers: modifiersErrors,
          scrollToModifierIdWithError
        };
        setErrors(newErrors);
        return currentProductErrors.length === 0;
      }, [product, selectedChoices, skippedModifiers, setErrors]);

      /* Skipped Modifiers */

      const setModifierSkipped = useCallback(
        (modifierId: string, skipped: boolean) => {
          if (!skipped) {
            setSkippedModifiers(without(skippedModifiers, modifierId));
            return;
          }

          if (skippedModifiers.includes(modifierId)) {
            return;
          }

          const modifier = getProductModifier(product, modifierId);
          setSelectedChoices(removeSelectedChoicesInModifier(modifier, selectedChoices));
          setSkippedModifiers(concat(skippedModifiers, [modifierId]));

          setModifierError(modifierId, undefined);
        },
        [product, selectedChoices, skippedModifiers, setSelectedChoices, setSkippedModifiers, setModifierError]
      );

      /* Choices */

      const addChoice = useCallback(
        (choiceId: string) => {
          const newSelectedChoices = addChoiceToSelections(product, choiceId, selectedChoices);
          setSelectedChoices(newSelectedChoices);

          const selectedModifier = getProductChoiceModifier(product, choiceId);
          const selectedModifierId = selectedModifier.id;
          const newSkippedModifiers = without(skippedModifiers, selectedModifierId);

          setSkippedModifiers(newSkippedModifiers);

          if(!errors.modifiers[selectedModifierId]) {
            return;
          }
          const modifierError = calculateModifierError(selectedModifier, newSelectedChoices, newSkippedModifiers);
          if(!modifierError) {
            setErrors({modifiers: omit(errors.modifiers, [selectedModifierId])});
          }
        },
        [product, selectedChoices, setSelectedChoices, skippedModifiers, setSkippedModifiers, errors, setErrors]
      );

      const removeChoice = useCallback(
        (choiceId: string) => {
          setSelectedChoices(removeChoiceFromSelections(product, choiceId, selectedChoices));
          setModifierError(getProductChoiceModifier(product, choiceId).id, undefined);
        },
        [product, selectedChoices, setSelectedChoices, setModifierError]
      );

      /* Modals */

      const openExtraChoices = useCallback((modifierId: string) => setModifierExtraChoicesId(modifierId), [setModifierExtraChoicesId]);
      const closeExtraChoices = useCallback(() => setModifierExtraChoicesId(undefined), [setModifierExtraChoicesId]);

      const openChoiceInfo = useCallback((modifierId: string) => setChoiceInfoId(modifierId), [setChoiceInfoId]);
      const closeChoiceInfo = useCallback(() => setChoiceInfoId(undefined), [setChoiceInfoId]);

      /* Finalize Product */

      const addToBasket = useCallback(async () => {
        if (!validateProduct()) {
          return;
        }

        const newProduct = createBasketNewProduct(product, 1, selectedChoices, specialinstructions);

        try {
          setIsLoading(true);
          if (typeof productBasketId === 'undefined') {
            const authtoken = getAuthtoken();
            await addProduct(restaurant.id, authtoken, newProduct);
          } else {
            newProduct.productid = productBasketId;
            await editProduct(newProduct);
          }
          setIsLoading(false);
          openBasket();
          history.push(`/${restaurant.id}/menu`);
        } catch (error) {
          setIsLoading(false);
          setErrorModalMessage(error);
        }
      }, [restaurant, product, selectedChoices, skippedModifiers, specialinstructions, productBasketId, validateProduct]);

      const isProductValid = getProductErrors(product, selectedChoices, skippedModifiers).length === 0;

      return (
        <div className="productPage">
          <div className="columns">
            <ProductBuilder
              errors={errors}
              product={product}
              selectedChoices={selectedChoices}
              skippedModifiers={skippedModifiers}
              specialinstructions={specialinstructions}
              addChoice={addChoice}
              setModifierSkipped={setModifierSkipped}
              setSpecialinstructions={setSpecialinstructions}
              openExtraChoices={openExtraChoices}
              openChoiceInfo={openChoiceInfo}
              toggleMobileBag={toggleMobileBag}
            />
            <Bag
              shownInMobile={openMobileBag}
              product={product}
              mode={productBasketId === undefined ? Mode.creation : Mode.edition}
              selectedChoices={selectedChoices}
              skippedModifiers={skippedModifiers}
              isProductValid={isProductValid}
              removeChoice={removeChoice}
              addToBasket={addToBasket}
            />
          </div>
          <div className="addToBasketButtonWrapper">
            <AddToBasketButton isProductValid={isProductValid} mode={productBasketId === undefined ? Mode.creation : Mode.edition} addToBasket={addToBasket} />
          </div>

          <ExtraChoicesModal
            product={product}
            selectedChoices={selectedChoices}
            modifierId={modifierExtraChoicesId}
            addChoice={addChoice}
            close={closeExtraChoices}
          />
          <ChoiceInfoModal product={product} choiceId={choiceInfoId} close={closeChoiceInfo} />
          <RestaurantValidator restaurant={restaurant} />
        </div>
      );
    })
  )
);

function getProductFromBasket(basket: OloBasket, basketProductId: string): OloBasketProduct {
  const basketProduct = find(basket.products, { id: basketProductId });
  if (!basketProduct) {
    throw new Error('Your are trying to change a product that was not found in the basket');
  }
  return basketProduct;
}

function getSelectedChoicesFromBasket(basket: OloBasket, basketProduct: OloBasketProduct): OloBasketNewProductChoice[] {
  return map(basketProduct.choices, choice => {
    return { choiceid: choice.optionid, quantity: choice.quantity, customfields: choice.customfields };
  });
}

function getSkippedModifiersFromBasket(product: OloRestaurantMenuProduct, basket: OloBasket, basketProduct: OloBasketProduct) {
  const modifiersIds = map(product.modifiers, 'id');

  const modifierChoiceTuples = flatten(
    map(product.modifiers, modifier => {
      return map(modifier.choices, choice => {
        return { choiceid: choice.id, modifierid: modifier.id };
      });
    })
  );

  const selectedChoiceModifiers = uniq(
    map(basketProduct.choices, selectedChoice => {
      const modifierChoiceTuple = find(modifierChoiceTuples, { choiceid: selectedChoice.optionid });
      if (!modifierChoiceTuple) {
        throw new Error('Invalid choice for this product');
      }
      return modifierChoiceTuple.modifierid;
    })
  );

  return difference(modifiersIds, selectedChoiceModifiers);
}

function getSkippedModifiers(product: OloRestaurantMenuProduct, preSelectedChoices: OloBasketNewProductChoice[]) {
  const optionalModifiers = filter(product.modifiers, modifier => {
    const preSelectedChoicesOfModifier = filter(modifier.choices, choice => some(preSelectedChoices, preSelectedChoice => preSelectedChoice.choiceid === choice.id));
    if(preSelectedChoicesOfModifier.length > 0) {
      return false;
    }
    return isModifierOptional(modifier);
  });
  return map(optionalModifiers, 'id');
}

function calculateModifierError(modifier: OloRestaurantMenuProductModifier, selectedChoices: OloBasketNewProductChoice[], skippedModifiers: string[] ) {
  //Ignore Extra Modifiers
  if (modifier.extraOf !== undefined) {
    return undefined;
  }

  // Ignore skipped modifiers
  if (skippedModifiers.find(skippedModifier => skippedModifier === modifier.id)) {
    return undefined;
  }

  // Check num selects
  const choicesInModifier = getSelectedChoicesInModifier(modifier, selectedChoices);

  if (choicesInModifier.length === 0 && isModifierOptional(modifier)) {
    return '*Please make a selection or choose Skip below.';
  }
  if (choicesInModifier.length === 0 && modifier.minselects === 0) {
    return '*Please make a selection to continue.';
  }
  if (choicesInModifier.length < modifier.minselects) {
    return `*Please make a selection to continue.${modifier.minselects > 1 ? ` (at least ${modifier.minselects} different options required)` : ''}`;
  }
  if (choicesInModifier.length > modifier.maxselects) {
    return `Please select fewer choices. (only ${modifier.maxselects === 1 ? 'one choice' : `${modifier.maxselects} different choices`} allowed)`;
  }

  // Check aggregate quantity
  const aggregateChoicesInModifier = sumBy(choicesInModifier, 'quantity');
  if (aggregateChoicesInModifier < modifier.minaggregatequantity) {
    return `*Please make a selection to continue.${
      modifier.minaggregatequantity > 1 ? ` (at least ${modifier.minaggregatequantity} total choices required)` : ''
    }`;
  }
  if (aggregateChoicesInModifier > modifier.maxaggregatequantity) {
    return `Please select fewer choices. (only ${modifier.maxselects === 1 ? 'one choice' : `${modifier.maxselects} total choices`} allowed)`;
  }

  return undefined;
}

function getProductErrors(product:OloRestaurantMenuProduct, selectedChoices: OloBasketNewProductChoice[], skippedModifiers: string[]) {
  return filter(
    map(product.modifiers, modifier => [modifier.id, calculateModifierError(modifier, selectedChoices, skippedModifiers)]),
    1
  );
}