import map from 'lodash/map';
import find from 'lodash/find';
import difference from 'lodash/difference';
import zipWith from 'lodash/zipWith';
import memoize from 'lodash/memoize';
import classnames from 'classnames';
import React, { useState, useEffect, useCallback } from 'react';
import { useHistory } from 'react-router-dom';

import { setIsLoading, setErrorModalMessage } from '../State/Global';
import { PaymentMethod } from '../Common/PaymentMethod';
import { DeliveryModePayload, Vehicle } from '../Common/DeliveryModePayload';
import { TimeModePayload } from '../Common/TimeModePayload';
import { NavigationBar, ErrorPage, RestaurantValidator } from '../Components';
import { makeWithPromiseWithProps } from '../Utilities/componentHelper';
import { logError } from '../Utilities/errorLogger';
import { isRestaurantOpen, getFirstAvailableDay } from '../Utilities/modelHelper';
import { getAsset } from '../Utilities/assetsHelper';

import { withUser, logoutGuestUser } from '../State/User';
import { withRestaurant } from '../State/CurrentRestaurant';
import {
  withBasket,
  setDeliveryMode,
  setPickupMode,
  setDispatchMode,
  setCurbsideMode,
  setTimeWanted,
  deleteTimeWanted,
  applyCoupon,
  addTip,
  removeCoupon,
  validateBasket,
  submitBasket
} from '../State/Basket';

import {
  OloUser,
  OloBasket,
  OloBasketValidation,
  OloRestaurant,
  OloRestaurantCalendar,
  OloTimemode,
  OloDeliverymode,
  OloDeliveryAddress,
  OloCoupon,
  getRestaurantCalendar
} from '../OloAPI';

import { Header } from './Header';
import { Summary } from './Summary';
import { DeliveryModePayloadSelector } from './DeliveryModePayloadSelector';
import { TimeSelector } from './TimeSelector';
import { PaymentMethodSelector } from './PaymentMethodSelector';
import { GuestUserInfoFields } from './GuestUserInfoFields';
import { TipField } from './TipField';
/*
Basket validation should be done inmediatelly before
submission. Validation is necessary to get the final taxes
*/
const withBasketValidation = makeWithPromiseWithProps(
  'initialBasketValidation',
  (props: { basket: OloBasket | undefined; calendar: OloRestaurantCalendar; restaurant: OloRestaurant }) => async () => {
    if(!props.basket) {
      throw new Error('Could not load the Basket');
    }

    try {
      await deleteTimeWanted(); //delete previous time wanted if exists
    }
    catch(error) {
      logError(error);
    }

    try {
      // if the restaurant is close the validation
      // is going to fail. The first available time
      // must be set as timewanted before validation
      if (!isRestaurantOpen(props.calendar, props.restaurant.timezone)) {
        const timewanted = getFirstAvailableDay(props.calendar, props.restaurant.timezone);
        if (timewanted) {
          await setTimeWanted(timewanted);
        }
      }

      return await validateBasket();
    }
    catch(error) {
      logError(error);
      return {
        basketId: props.basket.id,
        tax: props.basket.tax,
        customerhandoffcharge: props.basket.customerhandoffcharge,
        subtotal: props.basket.subtotal,
        total: props.basket.total
      }
    }
  },
  (props: { basket: OloBasket | undefined; calendar: OloRestaurantCalendar; restaurant: OloRestaurant }) => [props.basket?.id, props.calendar, props.restaurant]
);

const withCalendar = makeWithPromiseWithProps(
  'calendar',
  (props: { restaurant: OloRestaurant }) => async () => getRestaurantCalendar(props.restaurant.id, 4),
  (props: { restaurant: OloRestaurant }) => [props.restaurant?.id]
);

export const CheckoutPage = withRestaurant(
  withUser(
    withBasket(
      withCalendar(
        withBasketValidation(
          ({
            user,
            restaurant,
            basket,
            initialBasketValidation,
            calendar,
          }: {
            user: OloUser;
            restaurant: OloRestaurant;
            basket: OloBasket | undefined;
            initialBasketValidation: OloBasketValidation;
            calendar: OloRestaurantCalendar;
          }) => {
            const history = useHistory();

            useEffect(() => window.scrollTo(0, 0), [restaurant.id]);

            const [basketValidation, setBasketValidation] = useState<OloBasketValidation>(initialBasketValidation);

            const [submitErrorMessage, setSubmitErrorMessage] = useState<string>('');
            const [showValidationErrors, setShowValidationErrors] = useState<boolean>(false);

            //Delivery Mode settings
            const [deliverymodes] = useState([{value: 'pickup', label: 'Pickup'}, {value: 'curbside', label: 'Curbside'}, {'value': 'dispatch', 'label': 'Delivery'}]);
            const [deliveryModePayload, setDeliveryModePayload] = useState<DeliveryModePayload>(() => {
              if(!basket) {
                return { type: 'pickup', data: undefined };
              }
              if(basket.deliverymode === 'dispatch') {
                return { type: 'dispatch', data: basket.deliveryAddress };
              }

              return { type: basket.deliverymode, data: undefined };
            });

            const updateDeliverymode = useCallback((deliverymode: OloDeliverymode) => async () => {
              setDeliveryModePayload({
                type: deliverymode,
                data: undefined
              });

              try {
                if(deliverymode !== 'dispatch') {
                  await setDeliveryMode(deliverymode);
                  setBasketValidation(await validateBasket());
                }
              }
              catch(error) {
                setErrorModalMessage(error);
              }
            }, [setDeliveryModePayload, setBasketValidation]);

            const updateDeliveryModePayload = useCallback(async (newDeliveryModePaylodad: DeliveryModePayload) => {
              setDeliveryModePayload(newDeliveryModePaylodad);

              try {
                if(newDeliveryModePaylodad.type === 'dispatch' && checkObjectFields(newDeliveryModePaylodad.data, [])) {
                  await setDispatchMode(newDeliveryModePaylodad.data!);
                  setBasketValidation(await validateBasket());
                }
              }
              catch(error) {
                setErrorModalMessage(error);
              }
            }, [setDeliveryModePayload, setBasketValidation]);

            //Time mode settings
            const [timemodes, setTimemodes] = useState<Array<{value: OloTimemode, label: string}>>([{value: 'asap', label: 'as soon as possible'}, {value: 'advance', label: 'schedule time' }]);

            const [timeModePayload, setTimeModePayload] = useState<TimeModePayload>({
              type: basket ? basket.timemode : 'asap',
              data: undefined
            });

            const updateTimemode = useCallback(memoize((timemode: OloTimemode) => () => {
              setTimeModePayload({
                type: timemode,
                data: undefined
              });
             }), [setTimeModePayload]);


            //Payment Method
            const [selectedPaymentMethod, setSelectedPaymentMethod] = useState<PaymentMethod | undefined>(undefined);

            //Coupon Code
            const updateCoupon = useCallback(async (coupon: OloCoupon|undefined) => {
              setIsLoading(true);
              try {
                if(coupon) {
                  await applyCoupon(coupon.couponcode);
                }
                else {
                  await removeCoupon();
                }
                setBasketValidation(await validateBasket());
              }
              catch(error) {
                setErrorModalMessage(error);
              }
              finally {
                setIsLoading(false);
              }
            }, [setBasketValidation]);

            //Tip
            const updateTip = useCallback(async (amount: number) => {
              setIsLoading(true);
              try {
                await addTip(amount);
                setBasketValidation(await validateBasket());
              }
              catch(error) {
                setErrorModalMessage(error);
              }
              finally {
                setIsLoading(false);
              }
            }, [setBasketValidation]);

            const validateData = useCallback(() => {
              let isValid = true;

              //validating delivery data
              if(deliveryModePayload.type === 'dispatch' && !checkObjectFields(deliveryModePayload.data, [])) {
                isValid = false;
              }
              else if(deliveryModePayload.type === 'curbside' && !checkObjectFields(deliveryModePayload.data, ['make', 'model', 'color'])) {
                isValid = false;
              }

              //Validating time
              if (timeModePayload.type === 'advance' && !checkObjectFields(timeModePayload.data, [])) {
                isValid = false;
              }

              //Validate user
              if(user.type === "guest" && !checkObjectFields(user, ['firstname', 'lastname', 'emailaddress', 'contactnumber'])) {
                isValid = false;
              }

              if (!selectedPaymentMethod) {
                isValid = false;
              }

              setShowValidationErrors(!isValid);
              if(!isValid) {
                window.scrollTo({
                  top: 0,
                  left: 0,
                  behavior: 'smooth'
                });
              }
              return isValid;
            }, [setShowValidationErrors, deliveryModePayload, timeModePayload, user, selectedPaymentMethod]);

            const submitBasketHandler = useCallback(async (event: React.FormEvent<HTMLFormElement>) => {
              event.preventDefault();

              if(!validateData()) {
                return;
              }

              try {
                setIsLoading(true);

                if(deliveryModePayload.type === 'curbside') {
                  await setCurbsideMode(getCurbsideCustomfields(deliveryModePayload.data!, restaurant.customfields));
                }
                if(timeModePayload.type === 'asap') {
                  //set to asap mode
                  await deleteTimeWanted();
                }
                if(timeModePayload.type === 'advance') {
                  //set the time wanted
                  const timewanted = timeModePayload.data!.timewanted;
                  await setTimeWanted(timewanted);
                }

                await validateBasket();
                const orderStatus = await submitBasket(user, selectedPaymentMethod!);
                setIsLoading(false);
                if(user.type === 'guest') {
                  logoutGuestUser();
                }
                history.push(`/order/${orderStatus.id}`);
              } catch (error) {
                setSubmitErrorMessage(error.message);
                setIsLoading(false);

                try {
                  await deleteTimeWanted();
                }
                catch(deletedTimeError) {
                  logError(deletedTimeError);
                }
              }
            }, [validateData, deliveryModePayload, restaurant, timeModePayload, selectedPaymentMethod, user, setSubmitErrorMessage]);

            //Back to menu
            const backToMenu = useCallback(() => history.push(`/${restaurant.id}/menu`), [restaurant.id, history]);

            if (!basket || basket.products.length === 0) {
              return <ErrorPage error={new Error("Error: The basket is empty")} />;
            }

            return (
              <div className="checkoutPage">
                <NavigationBar />
                <Header restaurant={restaurant} calendar={calendar} deliverymode={basket.deliverymode} />
                <form onSubmit={submitBasketHandler}>
                  <div className="mainContainer">
                    <div className="fields">
                      <section>
                        {showValidationErrors ? <div className="errorMessage">*Please enter the required fields.</div> : null }
                        <h2>Handoff Method</h2>
                        <div className="toggle three-column">
                          {map(deliverymodes, deliverymode => (
                            <button
                              key={deliverymode.value}
                              onClick={updateDeliverymode(deliverymode.value as OloDeliverymode)}
                              className={classnames("blueButton", { selected: deliverymode.value === deliveryModePayload.type })}
                              type="button"
                            >
                              {deliverymode.label}
                            </button>
                          ))}
                        </div>

                        <DeliveryModePayloadSelector
                          user={user}
                          deliveryModePayload={deliveryModePayload}
                          updateDeliveryModePayload={updateDeliveryModePayload}
                          showValidationErrors={showValidationErrors}
                        />
                      </section>

                      <section>
                        <h2>Time details</h2>
                        <div className="toggle two-column">
                          {map(timemodes, timemode => (
                            <button
                              key={timemode.value}
                              onClick={updateTimemode(timemode.value)}
                              className={classnames("blueButton", { selected: timemode.value === timeModePayload.type})}
                              type="button"
                            >
                              {timemode.label}
                            </button>
                          ))}
                        </div>
                        <TimeSelector
                          calendar={calendar}
                          earliestreadytime={basket.earliestreadytime}
                          timezone={restaurant.timezone}
                          timeModePayload={timeModePayload}
                          updateTimeModePayload={setTimeModePayload}
                          showValidationErrors={showValidationErrors}
                        />
                      </section>

                      {user.type === 'guest' ?
                        (<section>
                          <h2>Personal Information</h2>
                          <GuestUserInfoFields user={user} showValidationErrors={showValidationErrors}/>
                        </section>)
                        : null
                      }

                      <section>
                        <h2>Payment Information</h2>
                        <PaymentMethodSelector
                          isGuestUser={user.type === 'guest'}
                          selectedPaymentMethod={selectedPaymentMethod}
                          updatePaymentMethod={setSelectedPaymentMethod}
                          showValidationErrors={showValidationErrors}
                        />
                      </section>

                      {basket.allowsTip ? (
                        <section>
                          <h2>Add a Tip</h2>
                          <TipField
                            subtotal={basketValidation.subtotal}
                            tip={basket.tip}
                            updateTip={updateTip}
                          />
                        </section>)
                      :null}

                    </div>
                    <div className="summaryWrapper">
                      <Summary
                        basketProducts={basket.products}
                        basketValidation={basketValidation}
                        coupon={basket.coupon}
                        updateCoupon={updateCoupon}
                        allowsTip={basket.allowsTip}
                        tip={basket.tip}
                      />
                    </div>
                  </div>

                  <div className="fields">
                    <div className="submit">
                      <div className="errorMessage">
                        If you encounter an issue placing your order, please re-enter your credit card information
                      </div>
                      {submitErrorMessage ? <div className="errorMessage">{"*" + submitErrorMessage}</div> : null}
                      <input type="submit" value="check out" />
                      <button type="button" className="backButton" onClick={backToMenu}>
                        <span>
                          <img src={getAsset("previous.svg")}/>
                          <span>back to menu</span>
                        </span>
                      </button>
                    </div>
                  </div>
                </form>
                <RestaurantValidator restaurant={restaurant} />
              </div>
            );
          }
        )
      )
    )
  )
);

function getCurbsideCustomfields(vehicle: Vehicle, customfields: Array<{id: string, label: string }>) {
  const labels = [OLO_MAKE_LABEL, OLO_MODEL_LABEL, OLO_COLOR_LABEL];
  const ids = map(labels, label => find(customfields, { label })!.id);
  const values = [vehicle.make, vehicle.model, vehicle.color];

  return zipWith(ids, values, (id, value) => {
    return { id, value };
  });
}

function checkObjectFields(object: any, fields: string[]) {
  if(object === undefined) {
    return false;
  }

  for(let i = 0; i < fields.length; i++) {
    const field = fields[i];
    if (!object.hasOwnProperty(field)) {
      return false;
    }
    if(object[field] === "") {
      return false;
    }
  }

  return true;
}