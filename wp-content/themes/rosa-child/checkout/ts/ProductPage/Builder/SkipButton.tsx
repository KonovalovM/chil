import React, { useCallback } from 'react';
import classnames from 'classnames';
import { getAsset } from '../../Utilities/assetsHelper';

export interface SkipButtonProps {
  modifierId: string;
  skipped: boolean;
  setModifierSkipped: (modifierId: string, skipped: boolean) => unknown;
}

export const SkipButton = ({ skipped, modifierId, setModifierSkipped }: SkipButtonProps) => {
  const skip = useCallback(() => {
    setModifierSkipped(modifierId, !skipped);
  }, [setModifierSkipped, modifierId, skipped]);

  return (
    <div className="optionButton">
      <button onClick={skip}>
        <div className="wrapper">
          <img src={getAsset('option_skip.svg')} />
          <div className={classnames('border', { selected: skipped })} />
        </div>
      </button>
      <h3>Skip</h3>
    </div>
  );
};
