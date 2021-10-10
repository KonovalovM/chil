import React, { useCallback, useState } from 'react';
import map from 'lodash/map';
import find from 'lodash/find';
import classnames from 'classnames';
import { useHistory } from 'react-router-dom';

import { getCostString, getCaloriesString } from '../../Utilities/formatHelper';
import { getProductLayout } from '../../Utilities/modelHelper';
import { getAsset } from '../../Utilities/assetsHelper';

import { setIsLoading, setErrorModalMessage } from '../../State/Global';
import { closeBasket, editProduct, deleteProduct } from '../../State/Basket';
import { OloBasketProduct, OloRestaurantMenuProduct, OloRestaurantMenuProductModifierChoice, OloBasketNewProductChoice } from '../../OloAPI';
import { LoadingStatus, ProductLayout } from '../../Common/Types';
import { QuantityControl } from '../Controls';

interface BasketProductComponentProps {
  index: number;
  restaurantId: string;
  basketProduct: OloBasketProduct;
  product: OloRestaurantMenuProduct | undefined;
  productsStatus: LoadingStatus;
}

export function BasketProductComponent(props: BasketProductComponentProps) {
  const { index, restaurantId, basketProduct, product, productsStatus } = props;

  const deleteProductCallback = useCallback(async () => {
    setIsLoading(true);
    try {
      await deleteProduct(basketProduct.id);
      setIsLoading(false);
    } catch (error) {
      setIsLoading(false);
      setErrorModalMessage(error);
    }
  }, []);

  const editProductCallback = useCallback(
    async (
      quantity: number,
      specialinstructions: string,
      recipient: string | undefined,
      customdata: string | undefined,
      selectedChoices: OloBasketNewProductChoice[] | undefined
    ) => {
      setIsLoading(true);
      try {
        await editProduct({
          productid: basketProduct.id,
          quantity,
          specialinstructions,
          recipient,
          customdata,
          choices: selectedChoices,
        });

        setIsLoading(false);
        return true;
      } catch (error) {
        setIsLoading(false);
        setErrorModalMessage(error);
        return false;
      }
    },
    []
  );

  if (productsStatus === LoadingStatus.progress) {
    return <LoadingBasketProduct />;
  } else if (productsStatus === LoadingStatus.error || product === undefined) {
    return <BasketProductWithMinimunData index={index} basketProduct={basketProduct} deleteProductCallback={deleteProductCallback} />;
  }

  const productLayout = getProductLayout(product.modifiers);

  if (productLayout === ProductLayout.simple) {
    return (
      <SimpleBasketProductComponent
        index={index}
        basketProduct={basketProduct}
        product={product}
        deleteProductCallback={deleteProductCallback}
        editProductCallback={editProductCallback}
      />
    );
  } else if (productLayout === ProductLayout.complex) {
    return (
      <ComplexBasketProductComponent
        index={index}
        restaurantId={restaurantId}
        basketProduct={basketProduct}
        product={product}
        deleteProductCallback={deleteProductCallback}
        editProductCallback={editProductCallback}
      />
    );
  }

  return <BasketProductWithMinimunData index={index} basketProduct={basketProduct} deleteProductCallback={deleteProductCallback} />;
}

//Todo: add better animations
const LoadingBasketProduct = () => {
  return <div>Loading...</div>;
};

const BasketProductWithMinimunData = ({
  index,
  basketProduct,
  deleteProductCallback,
}: {
  index: number;
  basketProduct: OloBasketProduct;
  deleteProductCallback: () => Promise<unknown>;
}) => {
  return (
    <div className="basketProduct">
      <div className="header">
        <h2>{`${index}. ${basketProduct.name}`}</h2>
        <strong>{getCostString(basketProduct.totalcost)}</strong>
      </div>
      <div className="actionButtons">
        <button className="remove" onClick={deleteProductCallback}>
          REMOVE
        </button>
      </div>
    </div>
  );
};

function SimpleBasketProductComponent({
  index,
  basketProduct,
  product,
  deleteProductCallback,
  editProductCallback,
}: {
  index: number;
  basketProduct: OloBasketProduct;
  product: OloRestaurantMenuProduct;
  deleteProductCallback: () => Promise<unknown>;
  editProductCallback: (
    quantity: number,
    specialinstructions: string,
    recipient: string | undefined,
    customdata: string | undefined,
    selectedChoices: OloBasketNewProductChoice[] | undefined
  ) => Promise<unknown>;
}) {
  const [quantity, setQuantity] = useState<number>(basketProduct.quantity);
  const [hasQuantityChanged, setHasQuantityChanged] = useState<boolean>(false);

  const handleChangeProductQuantity = useCallback(
    (newQuantity: number) => {
      setHasQuantityChanged(true);
      setQuantity(newQuantity);
    },
    [setQuantity, setHasQuantityChanged]
  );

  const editProductHandler = useCallback(async () => {
    const success = await editProductCallback(quantity, basketProduct.specialinstructions, basketProduct.recipient, basketProduct.customdata, undefined);
    if (!success) {
      setQuantity(basketProduct.quantity);
    }
    setHasQuantityChanged(false);
  }, [quantity, basketProduct, setQuantity, setHasQuantityChanged, editProductCallback]);

  const calories = getCaloriesString(product.calories, basketProduct.quantity);

  return (
    <div className="basketProduct">
      <div className="header">
        <h2>{`${index}. ${basketProduct.name}`}</h2>
        <strong>{getCostString(basketProduct.totalcost)}</strong>
      </div>
      <small>{calories}</small>
      <QuantityControl
        quantity={quantity}
        minimumquantity={product.minimumquantity}
        maximumquantity={product.maximumquantity}
        quantityincrement={product.quantityincrement}
        setQuantity={handleChangeProductQuantity}
      />
      <div className="actionButtons">
        {hasQuantityChanged ? (
          <button onClick={editProductHandler}>UPDATE</button>
        ) : (
          <button className="remove" onClick={deleteProductCallback}>
            REMOVE
          </button>
        )}
      </div>
    </div>
  );
}

function ComplexBasketProductComponent({
  index,
  restaurantId,
  basketProduct,
  product,
  deleteProductCallback,
  editProductCallback,
}: {
  index: number;
  restaurantId: string;
  basketProduct: OloBasketProduct;
  product: OloRestaurantMenuProduct;
  deleteProductCallback: () => Promise<unknown>;
  editProductCallback: (
    quantity: number,
    specialinstructions: string,
    recipient: string | undefined,
    customdata: string | undefined,
    selectedChoices: OloBasketNewProductChoice[] | undefined
  ) => Promise<boolean>;
}) {
  const [recipient, setRecipient] = useState<string>(basketProduct.recipient);
  const [hasChange, setHasChange] = useState<boolean>(false);
  const history = useHistory();

  const changeProduct = useCallback(() => {
    history.push(`/${restaurantId}/products/${basketProduct.productId}/${basketProduct.id}`);
    closeBasket();
  }, [history]);

  const changeRecipientHandler = useCallback(
    (event: React.ChangeEvent<HTMLInputElement>) => {
      setRecipient(event.target.value);
      setHasChange(true);
    },
    [setRecipient, setHasChange]
  );

  const editRecipientHandler = useCallback(async () => {
    const selectedChoices = map(basketProduct.choices, choice => {
      return {
        choiceid: choice.optionid,
        quantity: choice.quantity,
        customfields: choice.customfields,
      };
    });

    await editProductCallback(basketProduct.quantity, basketProduct.specialinstructions, recipient, basketProduct.customdata, selectedChoices);

    setHasChange(false);
  }, [basketProduct, recipient, hasChange, setHasChange, editProductCallback]);

  const calories = getCaloriesString(product.calories);
  const choicesText = basketProduct.customdata || '';

  return (
    <div className="basketProduct">
      <div className="customerName">
        <h2>{index + '.'}</h2>
        <input type="text" placeholder="Type your name" onBlur={editRecipientHandler} onChange={changeRecipientHandler} value={recipient} />
        <img className={classnames({ active: hasChange })} src={getAsset('edit.svg')} />
      </div>
      <div className="header">
        <h2>{basketProduct.name}</h2>
        <strong>{getCostString(basketProduct.totalcost)}</strong>
      </div>
      <small>{calories}</small>
      <p>{choicesText}</p>
      <div className="actionButtons">
        <button className="remove" onClick={deleteProductCallback}>
          REMOVE
        </button>
        <button className="change" onClick={changeProduct}>
          CHANGE
        </button>
      </div>
    </div>
  );
}
