import { expect } from 'chai';
import 'mocha';
import {
  getRestaurants,
  getRestaurant,
  getRestaurantMenu,
  getRestaurantMenuProduct,
  getRestaurantMenuProductModifiers,
  getRestaurantMenuProductWithModifiers,
} from '../../OloAPI';
import * as OloAPIUtils from '../../OloAPI/utils';
import { loadEnv } from '../utils';
import simple from 'simple-mock';

import testdata from '../testdata.json';

let validRestaurantId = testdata.restaurantId;
let validProductId = testdata.simpleBasketProduct.productid;

before(async () => {
  loadEnv();
});

simple.mock(OloAPIUtils, 'getRestaurantImages').returnWith([]);

describe('Restaurants', () => {
  describe('getRestaurants', () => {
    it('Should be at least 1 restaurant', done => {
      getRestaurants().then(restaurants => {
        expect(restaurants.length).to.be.greaterThan(0);
        done();
      });
    });
  });

  describe('getRestaurant', () => {
    it('The restaurant should have a valid id', done => {
      getRestaurant(validRestaurantId).then(restaurant => {
        expect(restaurant.id).to.be.equal(validRestaurantId);
        done();
      });
    });

    it('Should fail when the id is not valid', done => {
      getRestaurant('-1')
        .then(restaurant => {
          done(new Error('It should fail'));
        })
        .catch(error => {
          expect(error).to.not.be.undefined;
          done();
        });
    });
  });

  describe('getRestaurantMenu', () => {
    it('The restaurant should have a valid id', done => {
      getRestaurantMenu(validRestaurantId).then(menu => {
        expect(menu.id).to.be.equal(validRestaurantId);
        done();
      });
    });

    it('Should fail when the id is not valid', done => {
      getRestaurantMenu('-1')
        .then(menu => {
          done(new Error('It should fail'));
        })
        .catch(error => {
          expect(error).to.not.be.undefined;
          done();
        });
    });
  });

  describe('getRestaurantMenuProduct', () => {
    it('The product should have a valid id', done => {
      getRestaurantMenuProduct(validRestaurantId, validProductId).then(product => {
        expect(product.id).to.be.equal(validProductId);
        done();
      });
    }).timeout(2000);

    it('Should fail when the id is not valid', done => {
      getRestaurantMenuProduct(validRestaurantId, '-1')
        .then(product => {
          done(new Error('It should fail'));
        })
        .catch(error => {
          expect(error).to.not.be.undefined;
          done();
        });
    });
  });

  describe('getRestaurantMenuProductModifiers', () => {
    it('The promise should resolve when the ids are valid', done => {
      getRestaurantMenuProductModifiers(validProductId).then(modifiers => {
        expect(modifiers).to.be.instanceof(Array);
        done();
      });
    });

    it('Should fail when the ids are not valid', done => {
      getRestaurantMenuProductModifiers('-1')
        .then(product => {
          done(new Error('It should fail'));
        })
        .catch(error => {
          expect(error).to.not.be.undefined;
          done();
        });
    });
  });

  describe('getRestaurantMenuProductWithModifiers', () => {
    it('The promise should resolve when the ids are valid', done => {
      getRestaurantMenuProductWithModifiers(validRestaurantId, validProductId).then(product => {
        expect(product.id).to.be.equal(validProductId);
        expect(product.modifiers).to.not.be.undefined;
        done();
      });
    });

    it('Should fail when the ids are invalid', done => {
      getRestaurantMenuProductWithModifiers(validRestaurantId, '-1')
        .then(product => {
          done(new Error('It should fail'));
        })
        .catch(error => {
          expect(error).to.not.be.undefined;
          done();
        });
    });
  });
});
