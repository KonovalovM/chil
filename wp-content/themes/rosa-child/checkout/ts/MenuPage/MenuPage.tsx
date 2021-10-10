import React, { useState, useEffect, useRef, useCallback } from 'react';

import find from 'lodash/find';
import map from 'lodash/map';
import flatten from 'lodash/flatten';
import zip from 'lodash/zip';
import cloneDeep from 'lodash/cloneDeep';

import { getAsset } from '../Utilities/assetsHelper';
import { makeWithPromiseWithProps } from '../Utilities/componentHelper';
import { usePromise } from '../Utilities/Hooks';

import { LoadingStatus } from '../Common/Types';
import { NavigationBar, SectionPositionTracker, RestaurantValidator, ErrorPage } from '../Components';

import { OloRestaurant, getRestaurantMenu, OloRestaurantMenuCategory, getRestaurantMenuProductModifiers } from '../OloAPI';
import { withRestaurant } from '../State/CurrentRestaurant';

import { CategoryComponent } from './CategoryComponent';
import { ProductModal } from './ProductModal';

const withCategories = makeWithPromiseWithProps(
  'categories',
  (props: { restaurant: OloRestaurant }) => async () => {
    const menu = await getRestaurantMenu(props.restaurant.id);
    const categories = menu.categories;
    if (!categories.length) {
      throw new Error('The menu is empty');
    }
    return categories;
  },
  (props: { restaurant: OloRestaurant }) => [props.restaurant.id]
);

export const MenuPage = withRestaurant(
  withCategories(({ restaurant, categories }: { restaurant: OloRestaurant; categories: OloRestaurantMenuCategory[] }) => {
    const sectionsRef = useRef<SectionPositionTracker<OloRestaurantMenuCategory>>(null);
    const [currentCategorySection, setCurrentCategorySection] = useState(categories[0]);

    const [currentProductId, setCurrentProductId] = useState<string | undefined>(undefined);

    const productModifiersInfoStatus = usePromise(async () => {
      const allProducts = flatten(map(categories, 'products'));
      const modifiersCollection = await Promise.all(map(allProducts, eachProduct => getRestaurantMenuProductModifiers(eachProduct.id)));
      const newProductModifiersInfo = map(zip(allProducts, modifiersCollection), ([eachProduct, modifiers]) => ({
        productId: eachProduct!.id,
        modifiers: modifiers!,
      }));
      return newProductModifiersInfo;
    }, [categories]);

    const getProductModifiersInfo = useCallback(() => {
      return productModifiersInfoStatus.status === LoadingStatus.success ? productModifiersInfoStatus.value : [];
    }, [productModifiersInfoStatus]);

    /* General setup */
    useEffect(
      () => () => {
        setCurrentProductId(undefined);
        setCurrentCategorySection(categories[0]);
      },
      [restaurant, setCurrentProductId, setCurrentCategorySection, categories]
    );

    useEffect(() => window.scrollTo(0, 0), [restaurant]);

    /* Modals */
    const openProductModal = useCallback((newCurrentProductId: string | undefined) => setCurrentProductId(newCurrentProductId), [setCurrentProductId]);
    const closeProductModal = useCallback(() => setCurrentProductId(undefined), [setCurrentProductId]);

    const updateSection = useCallback(
      (categorySection: OloRestaurantMenuCategory) => {
        const sectionsElem = sectionsRef.current;
        if (!sectionsElem) {
          return;
        }
        setCurrentCategorySection(categorySection);
        sectionsElem.scrollToSection(categorySection);
      },
      [setCurrentCategorySection, categories, sectionsRef]
    );

    const onSectionChange = useCallback(
      (categorySection: OloRestaurantMenuCategory) => {
        setCurrentCategorySection(categorySection);
      },
      [setCurrentCategorySection]
    );

    const scrollToTop = () => {
      window.scrollTo({
        top: 0,
        left: 0,
        behavior: 'smooth',
      });
    };

    if (productModifiersInfoStatus.status === LoadingStatus.error) {
      return <ErrorPage error={productModifiersInfoStatus.error} />;
    }

    const products = flatten(map(categories, 'products'));
    const product = find(products, { id: currentProductId });
    const currentProduct = product ? cloneDeep(product) : undefined;

    return (
      <React.Fragment>
        <NavigationBar sectionsData={categories} currentSection={currentCategorySection} updateSection={updateSection} />

        <div className="menuPage">
          <SectionPositionTracker ref={sectionsRef} sectionsData={categories} currentSection={currentCategorySection} onSectionChange={onSectionChange}>
            {map(categories, category => (
              <CategoryComponent
                key={category.id}
                restaurantId={restaurant.id}
                category={category}
                productModifiersInfo={getProductModifiersInfo()}
                openProductModal={openProductModal}
              />
            ))}
          </SectionPositionTracker>
          <div className="footer">
            <img src={getAsset('banner-bottom.png')} />
            <div className="textLayer">
              <p>Whoa, you reached the end! Our menu is always changing, so scroll back soon!</p>
              <button onClick={scrollToTop}>Back to top</button>
            </div>
          </div>
        </div>

        <ProductModal
          restaurantId={restaurant.id}
          product={currentProduct}
          productModifierInfo={find(getProductModifiersInfo(), { productId: currentProductId })}
          close={closeProductModal}
        />

        <RestaurantValidator restaurant={restaurant} />
      </React.Fragment>
    );
  })
);
