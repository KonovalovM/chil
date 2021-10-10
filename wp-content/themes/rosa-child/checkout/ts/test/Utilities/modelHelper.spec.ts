import { expect } from 'chai';
import 'mocha';
import { product } from './product';
import { OloBasketNewProductChoice } from '../../OloAPI';
import { addChoiceToSelections } from '../../Utilities/modelHelper';

describe('modelHelper/addChoiceToSelections', () => {
  describe('Modifier that does not support quantities and is mandatory', () => {
    it('The newChoices should be equal to an array with a choice when oldChoices is empty', () => {
      const newChoices = addChoiceToSelections(product, '996341998', []);
      expect(newChoices).to.eql([{ choiceid: '996341998', quantity: 1 }]);
    });
    it('The newChoices should include the new choice and remove the oldChoices of the modifier when the choiceid does not exists in oldChoices', () => {
      const newChoices = addChoiceToSelections(product, '996341999', [{ choiceid: '996341998', quantity: 1 }]);
      expect(newChoices).to.eql([{ choiceid: '996341999', quantity: 1 }]);
    });
    it('The newChoices should be an empty array when the choiceid already exists in oldChoices', () => {
      const newChoices = addChoiceToSelections(product, '996341998', [{ choiceid: '996341998', quantity: 1 }]);
      expect(newChoices).to.eql([]);
    });
  });

  describe('Modifier that support quantities and is not mandatory', () => {
    it('The newChoices should be equal to an aray with a choice when oldChoices is empty', () => {
      const newChoices = addChoiceToSelections(product, '996341992', []);
      expect(newChoices).to.eql([{ choiceid: '996341992', quantity: 2 }]);
    });
    it('The newChoices should contain the choice with the quantity incremented when the choiceid already exists in oldChoices and does not reach the maxchoicequantity', () => {
      const newChoices = addChoiceToSelections(product, '996341992', [
        { choiceid: '996341992', quantity: 2 },
        { choiceid: '996341993', quantity: 2 },
      ]);
      expect(newChoices).to.have.deep.members([
        { choiceid: '996341992', quantity: 4 },
        { choiceid: '996341993', quantity: 2 },
      ]);
    });
    it('The newChoices should contain the choice with the quantity incremented when the choiceid already exists in oldChoices and does not reach the maxchoicequantity', () => {
      const newChoices = addChoiceToSelections(product, '996341992', [
        { choiceid: '996341992', quantity: 4 },
        { choiceid: '996341993', quantity: 2 },
      ]);
      expect(newChoices).to.have.deep.members([
        { choiceid: '996341992', quantity: 6 },
        { choiceid: '996341993', quantity: 2 },
      ]);
    });
    it('The newChoices should not contain the choice when the choiceid already exists in oldChoices and reach the maxchoicequantity', () => {
      const newChoices = addChoiceToSelections(product, '996341992', [
        { choiceid: '996341992', quantity: 10 },
        { choiceid: '996341993', quantity: 2 },
      ]);
      expect(newChoices).to.eql([{ choiceid: '996341993', quantity: 2 }]);
    });
    it('The newChoices should be equal to oldChoices when the modifier does not have capaticy', () => {
      const oldChoices = [
        { choiceid: '996341992', quantity: 10 },
        { choiceid: '996341993', quantity: 8 },
        { choiceid: '996341997', quantity: 6 },
        { choiceid: '996341996', quantity: 2 },
        { choiceid: '996341995', quantity: 4 },
      ];
      const newChoices = addChoiceToSelections(product, '996341991', oldChoices);
      expect(newChoices).to.have.deep.members(oldChoices);
    });
  });

  describe('Modifier that support quantities and is not mandatory; and minchoicequantity is greather than choicequantityincrement', () => {
    it('The newChoices should contain the choice with a quantity iqual to minchoicequantity', () => {
      const newChoices = addChoiceToSelections(product, '996341986', []);
      expect(newChoices).to.eqls([{ choiceid: '996341986', quantity: 6 }]);
    });
    it('The newChoices should contain the choice with the quantity incremented when the choiceid already exists in oldChoices and does not reach the maxchoicequantity', () => {
      const newChoices = addChoiceToSelections(product, '996341986', [
        { choiceid: '996341986', quantity: 6 },
        { choiceid: '996341993', quantity: 2 },
      ]);
      expect(newChoices).to.have.deep.members([
        { choiceid: '996341986', quantity: 9 },
        { choiceid: '996341993', quantity: 2 },
      ]);
    });
    it('The newChoices should not contain the choice when the choiceid already exists in oldChoices and reach the maxchoicequantity', () => {
      const newChoices = addChoiceToSelections(product, '996341986', [
        { choiceid: '996341986', quantity: 9 },
        { choiceid: '996341993', quantity: 2 },
      ]);
      expect(newChoices).to.eql([{ choiceid: '996341993', quantity: 2 }]);
    });
    it('The newChoices should be equal to oldChoices when the modifier does not have capaticy', () => {
      const oldChoices = [
        { choiceid: '996341986', quantity: 9 },
        { choiceid: '996341981', quantity: 9 },
        { choiceid: '996341985', quantity: 9 },
      ];
      const newChoices = addChoiceToSelections(product, '996341989', oldChoices);
      expect(newChoices).to.have.deep.members(oldChoices);
    });
  });

  describe('Modifier that support quantities, is not mandatory and the maxaggregatequantity is less than maxchoicequantity*maxselects', () => {
    it('The newChoices should not include the newChoice when the quantity of newChoice makes reach the limit of maxaggregatequantity but not the limit of maxchoicequantity', () => {
      const oldChoices = [
        { choiceid: '996341970', quantity: 6 },
        { choiceid: '996341966', quantity: 6 },
        { choiceid: '996341965', quantity: 6 },
      ];
      const expectedChoices = [
        { choiceid: '996341970', quantity: 6 },
        { choiceid: '996341966', quantity: 6 },
      ];
      const newChoices = addChoiceToSelections(product, '996341965', oldChoices);
      expect(newChoices).to.have.deep.members(expectedChoices);
    });
  });
});
