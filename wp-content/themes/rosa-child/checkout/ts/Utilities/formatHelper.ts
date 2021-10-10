export function addZeroPadding(integer: number): string {
  return integer >= 0 && integer <= 9 ? `0${integer}` : String(integer);
}

export function getCostString(cost: number) {
  const options = {
    style: 'currency',
    currency: 'USD',
  };
  const formater = new Intl.NumberFormat('en-US', options);
  return formater.format(cost);
}

export function getCaloriesString(calories: number | { min: number; max: number }, quantity = 1) {
  if (typeof calories === 'number') {
    return `${calories * quantity} CALS`;
  }
  if (typeof calories === 'object') {
    const value = ((calories.min + calories.max) / 2) * quantity;
    return `${value.toFixed(0)} CALS`;
  }

  return '';
}

function reduce(numerator: number, denominator: number) {
  function gcd(a: number, b: number): number {
    return b ? gcd(b, a % b) : a;
  }
  const gcdValue = gcd(numerator, denominator);
  return [numerator / gcdValue, denominator / gcdValue];
}

export function quantityToString(quantity: number, denominator = 1) {
  if (!quantity) {
    return '';
  }
  if (denominator === 1) {
    return String(quantity);
  }
  const [newQuantity, newDenominator] = reduce(quantity, denominator);
  return newDenominator === 1 ? String(newQuantity) : `${newQuantity}/${newDenominator}`;
}

export function formatPhoneNumber(phoneNumber: string) {
  const chunks = [];
  for (let i = 0; i < phoneNumber.length; i += 3) {
    chunks.push(phoneNumber.substring(i, i + 3));
  }
  const numberChunks = chunks.length <= 3 ? chunks : [chunks[0], chunks[1], chunks[2] + chunks[3]];
  return numberChunks.join('-');
}

export function getDeliveryModeText(deliverymode: string) {
  if(deliverymode === 'curbside') {
    return 'Curbside Pickup';
  }
  if(deliverymode === 'dispatch') {
    return 'Delivery';
  }
  return 'Pickup';
}