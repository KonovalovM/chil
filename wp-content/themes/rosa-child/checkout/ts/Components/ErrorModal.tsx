import React, { useCallback } from 'react';
import Modal from 'react-modal';
import { observer } from 'mobx-react';
import { withGlobalState, GlobalState, setErrorModalMessage } from '../State/Global';
import { getAsset } from '../Utilities/assetsHelper';

export const ErrorModal = withGlobalState(
  observer(({ globalState }: { globalState: GlobalState }) => {
    const closeModal = useCallback(() => setErrorModalMessage(undefined), []);

    return (
      <Modal
        isOpen={globalState.errorMessage !== undefined}
        overlayClassName="noOverlay"
        className="simpleContent"
        closeTimeoutMS={200}
        shouldCloseOnOverlayClick={true}
        onRequestClose={closeModal}
      >
        <div className="errorModal">
          <button className="closeButton" onClick={closeModal}>
            <img src={getAsset('close.svg')} />
          </button>
          <h2>{globalState.errorMessage}</h2>
        </div>
      </Modal>
    );
  })
);
