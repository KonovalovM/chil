import map from 'lodash/map';
import filter from 'lodash/filter';
import find from 'lodash/find';
import concat from 'lodash/concat';
import flatten from 'lodash/flatten';
import assign from 'lodash/assign';
import { addDays } from 'date-fns';
import memoize from 'memoizee';
import { makeRequest } from './makeRequest';
import {
  dateToStringAPIFormat,
  parseCalories,
  parseAvailability,
  parseMetadata,
  parseImageUrl,
  getRestaurantImages,
  utcOffsetToTimeZone,
  stringToDate,
} from './utils';
import { getAsset } from '../Utilities/assetsHelper';

/* --------------------------------------------------------------------------*/
/* Types                                                                     */
/* --------------------------------------------------------------------------*/

export type OloRestaurantCalendar = Array<{ start: Date; end: Date }>;

export interface OloRestaurant {
  id: string;
  name: string;
  phone: string;
  img: string;
  latitude: number;
  longitude: number;
  timezone: string;

  streetaddress: string;
  crossstreet: string;
  city: string;
  state: string;
  zip: string;
  address: string;

  availability: { available: boolean; description: string };
  customfields: Array<{
    id: string;
    label: string;
  }>;
}

export interface OloRestaurantMenu {
  id: string;
  categories: OloRestaurantMenuCategory[];
}

export interface OloRestaurantMenuCategory {
  id: string;
  name: string;
  banner: {
    images: string[];
    title: string;
    subtitle: string;
  };
  products: OloRestaurantMenuProduct[];
  layout?: string;
}

export interface OloRestaurantMenuProduct {
  id: string;
  name: string;
  description: string;
  calories: number | { min: number; max: number };
  cost: number;
  availability: { available: boolean; description: string };

  minimumquantity: number;
  maximumquantity: number;
  quantityincrement: number;

  modifiers?: OloRestaurantMenuProductModifier[];

  img: string;
}

export interface OloRestaurantMenuProductModifier {
  id: string;
  description: string;
  explanationtext: string;
  availability: { available: boolean; description: string };

  supportschoicequantities: boolean;
  minaggregatequantity: number;
  maxaggregatequantity: number;
  minchoicequantity: number;
  maxchoicequantity: number;

  mandatory: boolean;
  minselects: number;
  maxselects: number;
  choicequantityincrement: number;

  choices: OloRestaurantMenuProductModifierChoice[];

  extraOf?: string;
  parentId?: string;
  fractional: boolean;
}

export interface OloRestaurantMenuProductModifierChoice {
  id: string;
  name: string;
  calories: number | { min: number; max: number };
  cost: number;
  availability: { available: boolean; description: string };
  isdefault: boolean;

  description: string;
  img: string;
}

/* --------------------------------------------------------------------------*/
/* Utility Functions                                                         */
/* --------------------------------------------------------------------------*/

function findRestaurantImage(restaurantId: string) {
  const restaurantImages = getRestaurantImages();
  const restaurantImage = find(restaurantImages, { restaurantId });
  return restaurantImage && restaurantImage.image ? restaurantImage.image : getAsset('defaultRestaurant.jpg');
}

// tslint:disable-next-line:no-any
function createRestaurant(restaurantData: any): OloRestaurant {
  if (!restaurantData.id) {
    throw new Error('Can not create a restaurant');
  }

  return {
    id: restaurantData.id.toString(),
    name: String(restaurantData.name),
    phone: String(restaurantData.telephone),
    img: findRestaurantImage(restaurantData.id.toString()),
    latitude: Number(restaurantData.latitude),
    longitude: Number(restaurantData.longitude),

    timezone: utcOffsetToTimeZone(Number(restaurantData.utcoffset)),
    streetaddress: String(restaurantData.streetaddress),
    crossstreet: String(restaurantData.crossstreet),
    city: String(restaurantData.city),
    state: String(restaurantData.state),
    zip: String(restaurantData.zip),

    address: `${restaurantData.streetaddress} ${restaurantData.state} ${restaurantData.zip}`,
    availability: {
      available: Boolean(restaurantData.isavailable),
      description: restaurantData.availabilitymessage,
    },
    customfields: map(restaurantData.customfields, customfield => {
      return {
        id: customfield.id,
        label: customfield.label
      }
    })
  };
}

// tslint:disable-next-line:no-any
function parseRestaurantMenuCategoryLayout(metadata: any): string {
  const possibleLayouts = ['grid', 'columns'];
  const layout = parseMetadata(metadata, 'layout');
  if (layout && possibleLayouts.includes(layout)) {
    return layout;
  }
  return possibleLayouts[0];
}

// tslint:disable-next-line:no-any
function createChoice(choiceData: any): OloRestaurantMenuProductModifierChoice {
  return {
    id: choiceData.id.toString(),
    name: String(choiceData.name),
    calories: parseCalories(choiceData.basecalories, choiceData.maxcalories),
    cost: Number(choiceData.cost),
    availability: parseAvailability(choiceData.availability),
    isdefault: Boolean(choiceData.isdefault),
    description: parseMetadata(choiceData.metadata, 'description') || '',
    img: parseMetadata(choiceData.metadata, 'image') || getAsset('option_default.svg'),
  };
}

// tslint:disable-next-line:no-any
function createModifier(modifierData: any, parentModifier?: OloRestaurantMenuProductModifier): OloRestaurantMenuProductModifier {
  const supportschoicequantities = Boolean(modifierData.supportschoicequantities);
  const mandatory = Boolean(modifierData.mandatory);
  return {
    id: modifierData.id.toString(),
    description: parentModifier ? `${parentModifier.description}: ${modifierData.description}` : String(modifierData.description),
    explanationtext: String(modifierData.explanationtext),
    availability: parseAvailability(modifierData.availability),
    supportschoicequantities,
    mandatory,
    maxaggregatequantity: supportschoicequantities ? Number(modifierData.maxaggregatequantity) : Infinity,
    minaggregatequantity: supportschoicequantities ? Number(modifierData.minaggregatequantity) : 0,
    maxchoicequantity: supportschoicequantities ? Number(modifierData.maxchoicequantity) : 1,
    minchoicequantity: supportschoicequantities ? Number(modifierData.minchoicequantity) : 0,
    minselects: mandatory ? 1 : modifierData.minselects ? Number(modifierData.minselects) : 0,
    maxselects: mandatory ? 1 : modifierData.maxselects ? Number(modifierData.maxselects) : Infinity,
    choicequantityincrement: mandatory ? 1 : Number(modifierData.choicequantityincrement),
    extraOf: parseMetadata(modifierData.metadata, 'extra-of'),
    parentId: parentModifier ? parentModifier.id : undefined,
    fractional: parseMetadata(modifierData.metadata, 'fractional') !== undefined ? true : false,
    choices: map(modifierData.options, createChoice),
  };
}

/* --------------------------------------------------------------------------*/
/* API Endpoints                                                             */
/* --------------------------------------------------------------------------*/

export const getRestaurants = memoize(
  async () => {
    const data = await makeRequest('/v1.1/restaurants', 'GET');
    return map(data.restaurants, createRestaurant);
  },
  { promise: true, maxAge: 5 * 60 * 1000 }
);

export const getRestaurant = memoize(
  async (idRestaurant: string) => {
    return createRestaurant(await makeRequest(`/v1.1/restaurants/${idRestaurant}`, 'GET'));
  },
  { promise: true, maxAge: 5 * 60 * 1000 }
);

export const getRestaurantMenu = memoize(
  async (idRestaurant: string): Promise<OloRestaurantMenu> => {
    const data = await makeRequest(`/v1.1/restaurants/${idRestaurant}/menu`, 'GET');

    return {
      id: idRestaurant,
      categories: map(data.categories, category => ({
        id: category.id.toString(),
        name: category.name,
        layout: parseRestaurantMenuCategoryLayout(category.metadata),
        banner: {
          images: map(['mobile-app', 'mobile-webapp-customize'], groupname => parseImageUrl(data.imagepath, category.images, groupname, getAsset('defaultBanner.png'))),
          title: category.name,
          subtitle: category.description,
        },
        products: map(category.products, product => ({
          id: product.id.toString(),
          name: product.name,
          description: product.description,
          calories: parseCalories(product.basecalories, product.maxcalories),
          cost: Number(product.cost),
          availability: parseAvailability(product.availability),
          minimumquantity: product.minimumquantity ? Number(product.minimumquantity) : 1,
          maximumquantity: product.maximumquantity ? Number(product.maximumquantity) : Infinity,
          quantityincrement: product.quantityincrement ? Number(product.quantityincrement) : 1,
          img: parseImageUrl(data.imagepath, product.images, 'mobile-webapp-customize', getAsset('product_default.svg')),
        })),
      })),
    };
  },
  { promise: true, maxAge: 5 * 60 * 1000 }
);
//todo: pass the timezone
export async function getRestaurantCalendar(idRestaurant: string, deltaDays: number): Promise<OloRestaurantCalendar> {
  const fromDate = new Date();
  const toDate = addDays(fromDate, deltaDays);

  const data = await makeRequest(
    `/v1.1/restaurants/${idRestaurant}/calendars?from=${dateToStringAPIFormat(fromDate)}&to=${dateToStringAPIFormat(toDate)}`,
    'GET'
  );
  const businessCalendar = find(data.calendar, { type: 'business' });
  if (!businessCalendar) {
    throw new Error('Incorrect calendar data');
  }

  return map(businessCalendar.ranges, range => {
    const start = stringToDate(range.start);
    const end = stringToDate(range.end);
    return { start, end };
  });
}

export const getRestaurantMenuProduct = memoize(
  async (restaurantId: string, productId: string): Promise<OloRestaurantMenuProduct> => {
    const menu = await getRestaurantMenu(restaurantId);
    const products = flatten(map(menu.categories, 'products'));
    const product = find(products, { id: productId });
    if (!product) {
      throw new Error(`Could not find product ${productId} on restaurant menu ${restaurantId}!`);
    }
    return product;
  },
  { promise: true, maxAge: 5 * 60 * 1000 }
);

export const getRestaurantMenuProductModifiers = memoize(
  async (productId: string) => {
    const modifiersData = await makeRequest(`/v1.1/products/${productId}/modifiers`, 'GET');
    const modifiers = flatten(
      map(modifiersData.optiongroups, modifierData => {
        const parent = createModifier(modifierData);
        const optionsWithModifiers = filter(modifierData.options, option => option.modifiers);
        const children = flatten(map(optionsWithModifiers, option => map(option.modifiers, childModifier => createModifier(childModifier, parent))));
        return concat([parent], children);
      })
    );
    return modifiers;
  },
  { promise: true, maxAge: 5 * 60 * 1000 }
);

export async function getRestaurantMenuProductWithModifiers(restaurantId: string, productId: string): Promise<OloRestaurantMenuProduct> {
  const [product, modifiers] = await Promise.all([getRestaurantMenuProduct(restaurantId, productId), getRestaurantMenuProductModifiers(productId)]);
  return assign({}, product, { modifiers });
}
