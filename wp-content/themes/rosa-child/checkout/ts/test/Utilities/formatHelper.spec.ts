import { expect } from 'chai';
import 'mocha';
import { getCaloriesString } from '../../Utilities/formatHelper';

describe('utils/getCaloriesString', function() {
  it('The resulting string should contain the value of calories when calories is a number and quantity is not set', function() {
    const calories = 2;
    expect(getCaloriesString(calories)).to.be.equal('2 CALS');
  });
  it('The resulting string should contain the value of calories*quantity when calories is a number and quantity is set', function() {
    const calories = 10;
    expect(getCaloriesString(calories, 2)).to.be.equal('20 CALS');
  });
  it('The resulting string should contain the average of calories when calories is an object and quantity is not set', function() {
    const calories = { min: 10, max: 25 };
    expect(getCaloriesString(calories)).to.be.equal('18 CALS');
  });
  it('The resulting string should contain the average of calories * quantity when calories is an object and quantity is set', function() {
    const calories = { min: 10, max: 20 };
    expect(getCaloriesString(calories, 3)).to.be.equal('45 CALS');
  });
});
