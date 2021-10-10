import React from 'react';
import find from 'lodash/find';
import map from 'lodash/map';
import { OloRestaurantMenuCategory, OloRestaurantMenuProduct } from '../OloAPI';
import { ProductGridTemplate, ProductColumnTemplate } from './ProductComponents';
import { ProductModifierInfo } from './Types';

export interface CategoryComponentProps {
  restaurantId: string;
  category: OloRestaurantMenuCategory;
  productModifiersInfo: ProductModifierInfo[];
  openProductModal: (productId: string | undefined) => void;
}

export const CategoryComponent = (props: CategoryComponentProps) => {
  return props.category.layout === 'grid' ? <GridTemplate {...props} /> : <ColumnsTemplate {...props} />;
};

const Banner = (props: { images: string[] }) => {
  return (
    <div className="banner">
      {map(props.images, (img, index) => <img key={index} src={img}/>)}
    </div>
  );
};

const GridTemplate = (props: CategoryComponentProps) => {
  const { restaurantId, category, openProductModal, productModifiersInfo } = props;

  return (
    <>
      <Banner {...category.banner} />
      <div className="category">
        <div className="gridTemplate">
          <div className="products">
            {map(category.products, (product: OloRestaurantMenuProduct) => {
              const productModifierInfo = find(productModifiersInfo, { productId: product.id });
              return (
                <ProductGridTemplate
                  key={product.id}
                  restaurantId={restaurantId}
                  product={product}
                  productModifierInfo={productModifierInfo}
                  openProductModal={openProductModal}
                />
              );
            })}
          </div>
        </div>
      </div>
    </>
  );
};

const ColumnsTemplate = (props: CategoryComponentProps) => {
  const { restaurantId, category, productModifiersInfo, openProductModal } = props;
  return (
    <>
      <Banner {...category.banner} />
      <div className="category">
        <div className="columnsTemplate">
          <div className="products">
            {map(category.products, (product: OloRestaurantMenuProduct) => {
              const productModifierInfo = find(productModifiersInfo, { productId: product.id });
              return (
                <ProductColumnTemplate
                  key={product.id}
                  restaurantId={restaurantId}
                  product={product}
                  openProductModal={openProductModal}
                  productModifierInfo={productModifierInfo}
                />
              );
            })}
          </div>
        </div>
      </div>
    </>
  );
};
