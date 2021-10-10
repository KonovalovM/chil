import { expect } from 'chai';
import 'mocha';
import _ from 'lodash';
import { loadEnv } from '../utils';

import { OloBasket, getBasket, createBasket, addBasketProduct, editBasketProduct, deleteBasketProduct, validateBasket } from '../../OloAPI';

import testdata from '../testdata.json';

const validRestaurantId = testdata.restaurantId;
let basketId = '';
const simpleBasketProduct = testdata.simpleBasketProduct;
const electionBasketProduct = testdata.electionBasketProduct;
const complexBasketProduct = testdata.complexBasketProduct;

before(async () => {
  loadEnv();
  const basket = await createBasket(validRestaurantId);
  basketId = basket.id;
});

describe('Basket', () => {
  describe('getBasket', () => {
    it('The id of the recovered basket should be equal to the basketId', done => {
      getBasket(basketId)
        .then(basket => {
          expect(basket.id).to.be.equal(basketId);
          done();
        })
        .catch(error => done(error));
    });
  });

  describe('addBasketProduct', () => {
    it('The basket returned should include the simple product', done => {
      addBasketProduct(basketId, simpleBasketProduct)
        .then(newBasket => {
          expect(_.some(newBasket.products, { productId: simpleBasketProduct.productid })).to.be.true;
          done();
        })
        .catch(error => done(error));
    });
  });

  describe('addBasketProduct', () => {
    it('The basket returned should include the election product', done => {
      addBasketProduct(basketId, electionBasketProduct)
        .then(newBasket => {
          expect(_.some(newBasket.products, { productId: electionBasketProduct.productid })).to.be.true;
          done();
        })
        .catch(error => done(error));
    });
  });

  describe('addBasketProduct', () => {
    it('The basket returned should include the complex product', done => {
      addBasketProduct(basketId, complexBasketProduct)
        .then(newBasket => {
          expect(_.some(newBasket.products, { productId: complexBasketProduct.productid })).to.be.true;
          done();
        })
        .catch(error => done(error));
    });
  });

  describe('editBasketProduct', () => {
    let basket: OloBasket | undefined = undefined;
    before(async () => {
      basket = await getBasket(basketId);
    });

    it('The product should be updated', done => {
      if (!basket) {
        done(new Error('Basket not found'));
        return;
      }

      const basketProduct = _.cloneDeep(electionBasketProduct);
      const existedBasketProduct = _.find(basket.products, { productId: basketProduct.productid });
      if (!existedBasketProduct) {
        done(new Error('The product you try to edit does not exits in the basket'));
        return;
      }
      basketProduct.productid = existedBasketProduct.id;
      basketProduct.quantity = 3;

      editBasketProduct(basketId, basketProduct)
        .then(newBasket => {
          const editedBasketProduct = _.find(newBasket.products, { id: basketProduct.productid });
          if (!editedBasketProduct) {
            done(new Error('The edited product was not found'));
            return;
          }

          expect(editedBasketProduct.quantity).to.be.equal(3);
          done();
        })
        .catch(error => done(error));
    });
  });

  describe('deleteBasketProduct', () => {
    let basket: OloBasket | undefined = undefined;
    before(async () => {
      basket = await getBasket(basketId);
    });

    it('The product has to be remove from the basket', done => {
      if (!basket) {
        done(new Error('Basket not found'));
        return;
      }

      const basketProduct = _.cloneDeep(complexBasketProduct);
      const existedBasketProduct = _.find(basket.products, { productId: basketProduct.productid });
      if (!existedBasketProduct) {
        done(new Error('The product you try to delete does not exits in the basket'));
        return;
      }

      deleteBasketProduct(basketId, existedBasketProduct.id)
        .then(newBasket => {
          const foundBasketProduct = _.find(newBasket.products, { id: existedBasketProduct.id });
          expect(foundBasketProduct).to.be.equal(undefined);
          done();
        })
        .catch(error => done(error));
    });
  });

  describe('validateBasket', () => {
    it('The basket validation should be valid', done => {
      validateBasket(basketId)
        .then(basketValidation => {
          expect(basketValidation.basketId).to.be.equal(basketId);
          done();
        })
        .catch(error => done(error));
    });
  });
});
