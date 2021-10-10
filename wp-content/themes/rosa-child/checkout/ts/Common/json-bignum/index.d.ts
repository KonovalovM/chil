declare module 'json-bignum' {
  export function parse(object: string): any;
  export function stringify(object: any): string;

  export class BigNumber {
    constructor(number: string) {}
  }
}
