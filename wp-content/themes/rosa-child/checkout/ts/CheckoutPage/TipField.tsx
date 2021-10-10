import map from 'lodash/map';
import round from 'lodash/round';
import findIndex from 'lodash/findIndex';
import includes from 'lodash/includes';
import memoize from 'lodash/memoize';
import classnames from 'classnames';
import React, { useState, useEffect, useCallback } from 'react';
import Modal from 'react-modal';
import { getCostString } from '../Utilities/formatHelper';
import { getAsset } from '../Utilities/assetsHelper';
import { CurrencyInput } from '../Components/FormInputs/CurrencyInput';

interface TipFieldProps {
	subtotal: number;
  tip: number;
  updateTip: (ammount: number) => Promise<void>;
}

function floatsEqual(value1: number, value2: number) {
	const diff = Math.abs(value1 - value2);
	return  diff <= Number.EPSILON;
}

function currencyToNumber(formatedValue: string) {
	return Number(formatedValue.replace(/[$,]/g,''));
}

export function TipField(props: TipFieldProps) {
	const { subtotal, tip, updateTip } = props;

	const [ isOpenTipModal, setIsOpenTipModal ] = useState<boolean>(false);

	const [ selectedToggleIndex, setSelectedToggleIndex] = useState<number>(0);

	useEffect(() => {
		if(tip === 0) {
			setSelectedToggleIndex(0);
		}
		else if(isPercentageSelected(15)) {
			setSelectedToggleIndex(1);
		}
		else if(isPercentageSelected(18)) {
			setSelectedToggleIndex(2);
		}
		else if(isPercentageSelected(20)) {
			setSelectedToggleIndex(3);
		}
		else {
			setSelectedToggleIndex(4);
		}
	}, [tip]);

	const isPercentageSelected = useCallback((tipPercentage: number) => {
		return floatsEqual(tip, round(subtotal*tipPercentage*0.01, 2));
	}, [subtotal, tip]);

	const selectTipPercentageHandler = useCallback(memoize((tipPercentage: number) => () => {
		if(isPercentageSelected(tipPercentage)) {
			return;
		}

		const newTip = round(subtotal*tipPercentage*0.01, 2);
		updateTip(newTip);
	}), [isPercentageSelected, subtotal, tip, updateTip]);

	const noTipHandler = useCallback(() => {
		if(tip === 0) {
			return;
		}

		updateTip(0);
	}, [tip, updateTip]);

	const openTipModal = useCallback(() => {
		setIsOpenTipModal(true);
	}, [setIsOpenTipModal]);

	const closeTipModal = useCallback(() => {
		setIsOpenTipModal(false);
	}, [setIsOpenTipModal]);

	const tipFormSubmitHandler = useCallback(async (newTip: number) => {
		await updateTip(newTip);
		closeTipModal();
	}, []);

	return (
		<>
			<div className="tipField">
				<TipButton
					text="No Tip"
					onClick={noTipHandler}
					icon={getAsset('no_tip.svg')}
					selected={selectedToggleIndex === 0}
				/>

				<TipButton
					text="15%"
					onClick={selectTipPercentageHandler(15)}
					upperText={getCostString(subtotal * 0.15)}
					selected={selectedToggleIndex === 1}
				/>

				<TipButton
					text="18%"
					onClick={selectTipPercentageHandler(18)}
					upperText={getCostString(subtotal * 0.18)}
					selected={selectedToggleIndex === 2}
				/>

				<TipButton
					text={"20%"}
					onClick={selectTipPercentageHandler(20)}
					upperText={getCostString(subtotal * 0.2)}
					selected={selectedToggleIndex === 3}
				/>

				<TipButton
					text={selectedToggleIndex === 4 ? `${getCostString(tip)}`: "Custom Tip"}
					upperText={selectedToggleIndex === 4 ? `${round(tip * 100 / subtotal, 0)}% Edit`: undefined }
					onClick={openTipModal}
					icon={selectedToggleIndex === 4 ? undefined: getAsset('edit_tip.svg')}
					selected={selectedToggleIndex === 4}
				/>
			</div>

			<Modal
				isOpen={isOpenTipModal}
				overlayClassName="modalOverlay"
        className="centeredContent"
        closeTimeoutMS={200}
        shouldCloseOnOverlayClick={true}
        onRequestClose={closeTipModal}
			>
				<TipForm
					subtotal={subtotal}
				  tip={tip}
				  cancel={closeTipModal}
  				onSubmit={tipFormSubmitHandler}
				/>
			</Modal>
		</>
	);
}

function TipButton(props: {text: string, icon?: string, upperText?: string, selected: boolean, onClick: () => void }) {
	const {text, icon, upperText, selected, onClick } = props;

	return (
		<button
			className={classnames('tipButton', 'blueButton', { selected })}
			onClick={onClick}
			type="button"
		>
			{ upperText ? <small>{upperText}</small> : null }
			{ icon ? <img src={icon}/> : null}
			<strong>{ text }</strong>
		</button>
	);
}

interface TipFormProps {
	subtotal: number;
  tip: number;
	cancel: () => void;
  onSubmit: (tip: number) => Promise<void>;
};

function TipForm(props: TipFormProps) {
	const { subtotal, tip, cancel, onSubmit } = props;

	/*Input Mode setting*/
	const [inputMode, setInputMode] = useState<'money'|'percentage'>('money');

	/*Current tip*/
	const [currentTip, setCurrentTip] = useState<number>(tip);

	const [moneyInputValue, setMoneyInputValue] = useState<string>(String(tip));
	const [percentageInputValue, setPercentageInputValue] = useState<string>(String(round(tip * 100 / subtotal, 0)));

	const onChangeMoneyInput = useCallback((value: string) => {
		const tipNumeric = currencyToNumber(value);

		setCurrentTip(tipNumeric);
		setMoneyInputValue(value);
		setPercentageInputValue(String(round(tipNumeric*100 / subtotal, 0)));
	}, [setCurrentTip, setMoneyInputValue, setPercentageInputValue]);

	const onChangePercentageInput = useCallback((event: React.ChangeEvent<HTMLInputElement>) => {
		const newPercentage = event.currentTarget.value;
		const numericPercentage = Number(newPercentage);
		const newTip = round(subtotal*numericPercentage*0.01, 2);

		setCurrentTip(newTip);
		setMoneyInputValue(String(newTip));
		setPercentageInputValue(newPercentage);
	}, [setCurrentTip, setMoneyInputValue, setPercentageInputValue]);


	const changeInputMode = useCallback((mode: 'money'|'percentage') => () => {
		setInputMode(mode);
	}, [setInputMode]);

	/* On submit */
	const handleOnSubmit = useCallback((event: React.FormEvent) => {
		event.preventDefault();
		event.stopPropagation();

		onSubmit(Number(currentTip));
	}, [currentTip, onSubmit]);

	const conversion = inputMode === 'money' ? `${round(Number(percentageInputValue), 0)}%` : getCostString(currentTip);

	return (
    <div className="modalForm tipForm">
      <form onSubmit={handleOnSubmit}>
        <div className="header">
          <h2>Custom Tip</h2>
          <button className="closeButton" type='button' onClick={cancel}>
  					<img src={getAsset('close.svg')}/>
          </button>
        </div>

        <div className='tipInputContainer'>
        	<button
        		type="button"
        		onClick={changeInputMode('money')}
        		className={classnames('moneyButton', 'blueButton', { selected: inputMode === 'money' })}
        	>
        		$
        	</button>
        	<button
        		type="button"
        		onClick={changeInputMode('percentage')}
        		className={classnames('percentageButton', 'blueButton', { selected: inputMode === 'percentage' })}
        	>
        		%
        	</button>

        	{inputMode === 'money' ?
        		<CurrencyInput
							placeholder=""
							name="tip"
							value={moneyInputValue}
							onChange={onChangeMoneyInput}
						/> : null
					}
        	{inputMode === 'percentage' ?
        		<input
        			type="number"
        			onChange={onChangePercentageInput}
        			value={percentageInputValue}
        		/> : null
        	}
        </div>
        <div className='conversion'>
        	<small>{`(${conversion})`}</small>
        </div>

        <input type="submit" value="ADD" />
      </form>
    </div>
  );
}