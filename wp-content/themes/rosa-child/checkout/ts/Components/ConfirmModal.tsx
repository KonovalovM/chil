import React from 'react';
import Modal from 'react-modal';
import { getAsset } from '../Utilities/assetsHelper';

interface ConfirmModalProps {
  isOpen: boolean;
  message: string;
  confirmMessage?: string;
  cancelMessage?: string;
  confirmCallback: () => void;
  cancelCallback: () => void;
}

export const ConfirmModal = ({ isOpen, message, confirmMessage, cancelMessage, confirmCallback, cancelCallback }: ConfirmModalProps) => {
  return (
    <Modal
      isOpen={isOpen}
      overlayClassName="modalOverlay"
      className="centeredContent"
      closeTimeoutMS={200}
      shouldCloseOnOverlayClick={true}
      onRequestClose={cancelCallback}
    >
      <div className="confirmModal">
        <div className="header">
          <button className="closeButton" onClick={cancelCallback}>
            <img src={getAsset('close.svg')} />
          </button>
        </div>
        <div className="info">
          <p>{message}</p>
        </div>
        <div className="buttonContainers">
          <button onClick={confirmCallback}>{confirmMessage || 'ok'}</button>
          <button onClick={cancelCallback}>{cancelMessage || 'cancel'}</button>
        </div>
      </div>
    </Modal>
  );
};
