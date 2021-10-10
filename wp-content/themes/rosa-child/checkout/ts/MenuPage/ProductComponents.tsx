import React, { useCallback } from 'react';
import classnames from 'classnames';
import { OloRestaurantMenuProduct } from '../OloAPI';
import { Link } from 'react-router-dom';
import { getCostString, getCaloriesString } from '../Utilities/formatHelper';
import { getProductLayout } from '../Utilities/modelHelper';
import { ProductLayout } from '../Common/Types';
import { ProductModifierInfo } from './Types';

export interface ProductComponentProps {
  restaurantId: string;
  product: OloRestaurantMenuProduct;
  productModifierInfo: ProductModifierInfo | undefined;
  openProductModal: (productId: string | undefined) => void;
}

export const ProductGridTemplate = (props: ProductComponentProps) => {
  const { product } = props;

  const openProductModalHandler = useCallback(() => {
    props.openProductModal(product.id);
  }, [product.id, props.openProductModal]);

  return (
    <div className={classnames('productGridTemplate', { notAvailable: !product.availability.available })}>
      <div className='mainContainer'>
        <div className='imageContainer'>
          <div className='imageWrapper'>
            <img src={product.img} onClick={openProductModalHandler} />
          </div>
        </div>
        <div className='priceContainer'>
          <strong>{getCostString(product.cost)}</strong>
        </div>
      </div>
      <div className='secondaryContainer'>
        <div className='titleContainer'>
          <h2 onClick={openProductModalHandler}>{product.name}</h2>
        </div>
        <div className='buttonContainer'>
          <AddButton {...props} />
        </div>
      </div>
    </div>
  );
};

export const ProductColumnTemplate = (props: ProductComponentProps) => {
  const { product } = props;
  const costString = getCostString(product.cost);
  const openProductModalHandler = useCallback(() => {
    props.openProductModal(product.id);
  }, [product.id, props.openProductModal]);
  return (
    <div className={classnames('productColumnTemplate', { notAvailable: !product.availability.available })}>
      <div className='infoRow'>
        <div className='info'>
          <h2 onClick={openProductModalHandler}>{product.name}</h2>
          <div className='detailsRow'>
            <small>{getCaloriesString(product.calories)}</small>
            <strong className='hiddenPrice'>{costString}</strong>
          </div>
        </div>
        <span>
          <strong className='price'>{costString}</strong>
          <AddButton {...props} />
        </span>
      </div>
      <div className="description">
        <p>{product.description}</p>
      </div>
    </div>
  );
};

const AddButton = (props: ProductComponentProps) => {
  const { restaurantId, product, productModifierInfo, openProductModal } = props;

  const modifiers = productModifierInfo ? productModifierInfo.modifiers : undefined;
  const productLayout = getProductLayout(modifiers);

  const openProductModalHandler = useCallback(() => {
    openProductModal(product.id);
  }, [product.id, openProductModal]);

  if (!productModifierInfo) {
    return <div>Loading...</div>;
  }

  if (!product.availability.available) {
    return null;
  }

  if (productLayout === ProductLayout.simple) {
    return (
      <div className="addButton">
        <button onClick={openProductModalHandler}>ADD TO BASKET</button>
      </div>
    );
  }

  return (
    <div className="addButton">
      <Link to={`/${restaurantId}/products/${product.id}/`}>CUSTOMIZE</Link>
    </div>
  );
};
