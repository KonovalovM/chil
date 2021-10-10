import React, { useCallback, useState, useEffect } from 'react';
import { observer } from 'mobx-react';
import { useHistory } from 'react-router';
import Modal from 'react-modal';
import { OloRestaurant, getRestaurant } from '../OloAPI';
import { withBasketState, BasketState, cleanBasket } from '../State/Basket';
import { setIsLoading, setErrorModalMessage } from '../State/Global';
import { LoadingStatus } from '../Common/Types';
import { usePromise } from '../Utilities/Hooks';

export const RestaurantValidator = withBasketState(
  observer(({ basketState, restaurant }: { basketState: BasketState; restaurant: OloRestaurant }) => {
    const history = useHistory();
    const [isOpen, setOpen] = useState(false);
    const basketRestaurantId = basketState.status === LoadingStatus.success && basketState.data ? basketState.data.restaurantId : undefined;

    const basketRestaurantStatus = usePromise(async () => (basketRestaurantId ? getRestaurant(basketRestaurantId) : undefined), [
      basketState,
      basketRestaurantId,
    ]);

    useEffect(() => setOpen(basketRestaurantId !== undefined && basketRestaurantId !== restaurant.id), [restaurant.id, setOpen, basketRestaurantId]);

    const goToRestaurant = useCallback(() => history.push(`/${basketRestaurantId}/menu`), [basketRestaurantId, history]);

    const cleanBasketHandler = useCallback(async () => {
      try {
        setIsLoading(true);
        cleanBasket();
        setOpen(false);
        setIsLoading(false);
        history.push(`/${restaurant.id}/menu`);
      } catch (error) {
        setIsLoading(false);
        setErrorModalMessage(error);
      }
    }, [restaurant]);

    if (basketRestaurantId === undefined) {
      return null;
    }

    let content = null;
    if (basketRestaurantStatus.status === LoadingStatus.error) {
      content = <ErrorContent errorMessage={'Can\'t recover the restaurant data'} />;
    } else if (basketRestaurantStatus.status === LoadingStatus.progress) {
      content = <LoadingContent />;
    } else if (basketRestaurantStatus.value) {
      content = (
        <RestaurantValidatorContent basketRestaurant={basketRestaurantStatus.value} goToRestaurant={goToRestaurant} cleanBasketHandler={cleanBasketHandler} />
      );
    }

    return (
      <Modal isOpen={isOpen} overlayClassName="modalOverlay" className="centeredContent" closeTimeoutMS={200}>
        {content}
      </Modal>
    );
  })
);

interface RestaurantValidatorContentProps {
  basketRestaurant: OloRestaurant;
  goToRestaurant: () => void;
  cleanBasketHandler: () => void;
}

function RestaurantValidatorContent(props: RestaurantValidatorContentProps) {
  const { basketRestaurant, goToRestaurant, cleanBasketHandler } = props;

  return (
    <div className="restaurantValidator">
      <div className="imgContainer">
        <div className="imageCover" />
        <img src={basketRestaurant.img} />
      </div>
      <div className="info">
        <h2>You're looking at a different restaurant</h2>
        <p>
          {`You already have some items in your cart from ${basketRestaurant.name}.
      In order to continue, please either switch to that restaurant or
      clear your cart.`}
        </p>
      </div>
      <div className="buttonContainers">
        <button onClick={goToRestaurant}>{`CONTINUE TO ${basketRestaurant.name.toUpperCase()}`}</button>
        <button onClick={cleanBasketHandler}>CLEAN CART</button>
      </div>
    </div>
  );
}

function ErrorContent({ errorMessage }: { errorMessage: string }) {
  return <div className="restaurantValidator">{errorMessage}</div>;
}

function LoadingContent() {
  return <div className="restaurantValidator">Loading ...</div>;
}
