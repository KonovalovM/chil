import { expect } from 'chai';
import 'mocha';
import _ from 'lodash';
import { loadEnv, createRandomString } from '../utils';

import { OloOrderStatus, getOrderStatus } from '../../OloAPI';

import testdata from '../testdata.json';

const orderId = testdata.orderId;

before(async () => {
  loadEnv();
});

describe('Orders', () => {
  describe('getOrderStatus', () => {
    it('The order has to be received', done => {
      getOrderStatus(orderId)
        .then(orderStatus => {
          expect(orderStatus.id).to.be.equal(orderId);
          done();
        })
        .catch(error => done(error));
    });

    it('It has to fail if the order does not exist', done => {
      getOrderStatus('fakeid')
        .then(orderStatus => {
          done(new Error('It must not returns an order'));
        })
        .catch(error => done());
    });
  });
});
