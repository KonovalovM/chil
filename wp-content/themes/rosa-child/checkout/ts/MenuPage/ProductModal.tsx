import React, { useCallback, useState } from 'react';
import Modal from 'react-modal';
import { Link } from 'react-router-dom';

import { OloRestaurantMenuProduct, OloRestaurantMenuProductModifierChoice, OloBasketNewProduct } from '../OloAPI';

import { getAsset } from '../Utilities/assetsHelper';
import { getCostString, getCaloriesString } from '../Utilities/formatHelper';
import { useModalValues } from '../Utilities/Hooks';
import { getProductLayout } from '../Utilities/modelHelper';

import { QuantityControl } from '../Components';
import { ProductLayout } from '../Common/Types';

import { addProduct, openBasket, createBasketNewProduct } from '../State/Basket';
import { getAuthtoken } from '../State/User';
import { setIsLoading } from '../State/Global';

import { ProductModifierInfo } from './Types';

interface ProductModalProps {
  restaurantId: string;
  product: OloRestaurantMenuProduct | undefined;
  productModifierInfo: ProductModifierInfo | undefined;
  close: () => void;
}

interface ProductModalData extends ProductModalProps {
  product: OloRestaurantMenuProduct;
  errorMessage: string | undefined;
  addToBasket: (newBasketProduct: OloBasketNewProduct) => void;
}

export function ProductModal({ restaurantId, product, productModifierInfo, close }: ProductModalProps) {
  const [isOpen, usedProduct] = useModalValues<OloRestaurantMenuProduct | undefined>(product);
  const [errorMessage, setErrorMessage] = useState<string>('');

  const addToBasket = useCallback(
    async (newBasketProduct: OloBasketNewProduct) => {
      setIsLoading(true);
      try {
        const authtoken = getAuthtoken();
        await addProduct(restaurantId, authtoken, newBasketProduct);
        close();
        openBasket();
        setIsLoading(false);
      } catch (error) {
        setIsLoading(false);
        setErrorMessage(`*Error: ${error.message}`);
      }
    },
    [restaurantId, close, setErrorMessage]
  );

  return (
    <Modal
      isOpen={isOpen}
      overlayClassName="modalOverlay"
      className="centeredContent"
      closeTimeoutMS={200}
      shouldCloseOnOverlayClick={true}
      onRequestClose={close}
    >
      {usedProduct ? (
        <ModalContent
          restaurantId={restaurantId}
          product={usedProduct}
          productModifierInfo={productModifierInfo}
          close={close}
          errorMessage={errorMessage}
          addToBasket={addToBasket}
        />
      ) : null}
    </Modal>
  );
}

function ModalContent(props: ProductModalData) {
  const { productModifierInfo } = props;
  const modifiers = productModifierInfo ? productModifierInfo.modifiers : undefined;
  const productLayout = getProductLayout(modifiers);

  if (productLayout === ProductLayout.simple) {
    return <SimpleProductModalContent {...props} />;
  }

  return <ComplexProductModalContent {...props} />;
}

function ComplexProductModalContent({ product, restaurantId, addToBasket, errorMessage, close }: ProductModalData) {
  return (
    <div className="productModal">
      <button className="closeButton" onClick={close}>
        <img src={getAsset('close-light.svg')} />
      </button>
      <div className="imgContainer">
        <div className="imageCover" />
        <img src={product.img} />
      </div>
      <div className="info">
        {errorMessage ? <div className="errorMessage">{errorMessage}</div> : null}
        <h2>{product.name}</h2>
        <strong>{getCostString(product.cost)}</strong>
        <small>{getCaloriesString(product.calories)}</small>
        <p>{product.description}</p>
      </div>
      <div className="controls">
        <Link to={`/${restaurantId}/products/${product.id}/`}>
          <button className="addTobasket">CUSTOMIZE</button>
        </Link>
      </div>
    </div>
  );
}

function SimpleProductModalContent({ product, addToBasket, errorMessage, close }: ProductModalData) {
  const { minimumquantity, maximumquantity, quantityincrement } = product;

  const [quantity, setQuantity] = useState<number>(minimumquantity);

  const addToBasketHandler = useCallback(async () => {
    addToBasket(createBasketNewProduct(product, quantity, []));
  }, [product, quantity, addToBasket]);

  const cost = getCostString(product.cost * quantity);

  return (
    <div className="productModal">
      <button className="closeButton" onClick={close}>
        <img src={getAsset('close-light.svg')} />
      </button>
      <div className="imgContainer">
        <div className="imageCover" />
        <img src={product.img} />
      </div>
      <div className="info">
        {errorMessage ? <div className="errorMessage">{errorMessage}</div> : null}
        <h2>{product.name}</h2>
        <strong>{cost}</strong>
        <small>{getCaloriesString(product.calories)}</small>
        <p>{product.description}</p>
      </div>
      <div className="controls">
        <QuantityControl
          quantity={quantity}
          minimumquantity={minimumquantity}
          maximumquantity={maximumquantity}
          quantityincrement={quantityincrement}
          setQuantity={setQuantity}
        />

        <button className="addTobasket" onClick={addToBasketHandler}>
          {'ADD TO BASKET + ' + cost}
        </button>
      </div>
    </div>
  );
}
