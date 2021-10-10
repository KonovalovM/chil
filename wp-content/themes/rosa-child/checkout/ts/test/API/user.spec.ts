import { expect } from 'chai';
import 'mocha';
import _ from 'lodash';
import { loadEnv, createRandomString } from '../utils';

import { OloUser, getUser, getContactNumber, updateContactNumber, updateUser, authenticateUser } from '../../OloAPI';

import testdata from '../testdata.json';

let user: OloUser | undefined = undefined;

before(async () => {
  loadEnv();
  const userData = testdata.userData;
  user = await authenticateUser(userData.email, userData.password);
});

describe('User', () => {
  describe('getUser', () => {
    it('The user should be recovered', done => {
      getUser(user!.authtoken)
        .then(recoveredUser => {
          expect(recoveredUser.emailaddress).to.be.equal(user!.emailaddress);
          done();
        })
        .catch(error => done(error));
    });
  });

  describe('getContactNumber', () => {
    it('The number should have ten digits', done => {
      getContactNumber(user!.authtoken)
        .then(contactnumber => {
          expect(contactnumber.length).to.be.equal(10);
          done();
        })
        .catch(error => done(error));
    });
  });

  describe('updateContactNumber', () => {
    it('The number should be updated', done => {
      const newContactnumber = createRandomString('0123456789', 10);
      updateContactNumber(user!.authtoken, newContactnumber)
        .then(contactnumber => {
          expect(contactnumber).to.be.equal(newContactnumber);
          done();
        })
        .catch(error => done(error));
    });

    it('The updating must fail when the contact number is not valid', done => {
      const newContactnumber = createRandomString('0123456789', 9);
      updateContactNumber(user!.authtoken, newContactnumber)
        .then(contactnumber => {
          done(new Error('The number is invalid'));
        })
        .catch(error => done());
    });
  });

  describe('updateUser', () => {
    it('The user data should be updated', done => {
      const newFirstname = createRandomString('abcdefghijkmpoq', 7);
      updateUser(user!.authtoken, newFirstname, user!.lastname, user!.emailaddress)
        .then(editedUser => {
          expect(editedUser.firstname).to.be.equal(newFirstname);
          done();
        })
        .catch(error => done(error));
    });
  });
});
