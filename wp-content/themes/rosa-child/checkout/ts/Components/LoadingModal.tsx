import React from 'react';
import Modal from 'react-modal';
import { observer } from 'mobx-react';
import { withGlobalState, GlobalState } from '../State/Global';

export const LoadingModal = withGlobalState(
  observer(({ globalState }: { globalState: GlobalState }) => {
    return (
      <Modal isOpen={globalState.isLoading} overlayClassName="noOverlay" className="simpleContent" closeTimeoutMS={200}>
        <div className="loadingModal">
          <h1>Loading ...</h1>
        </div>
      </Modal>
    );
  })
);
