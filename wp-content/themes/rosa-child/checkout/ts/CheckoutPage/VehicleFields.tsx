import memoize from 'lodash/memoize';
import React, { useState, useEffect, useCallback } from 'react';
import { DeliveryModePayload, Vehicle } from '../Common/DeliveryModePayload';

interface VehicleFieldsProps {
  deliveryModePayload: DeliveryModePayload;
  updateDeliveryModePayload: (deliveryModePayload: DeliveryModePayload) => void;
  showValidationErrors: boolean;
}

export function VehicleFields({ deliveryModePayload, updateDeliveryModePayload, showValidationErrors }: VehicleFieldsProps) {

  const [vehicle, setVehicle] = useState<Vehicle>( deliveryModePayload.type === 'curbside' && deliveryModePayload.data ? deliveryModePayload.data : { make: '', model: '', color: '' });

  useEffect(() => {
    updateDeliveryModePayload({
      type: 'curbside',
      data: vehicle
    });
  }, [vehicle]);

  const handleInputChange = useCallback(
    memoize((attribute: string) => (event: React.ChangeEvent<HTMLInputElement>) => {
      const newVehicle = {...vehicle, [attribute]: event.currentTarget.value };

      setVehicle(newVehicle);
    }),
    [vehicle, setVehicle]
  );

  return (
    <div className="guestUserInfoFields">
      <h3>Enter Vehicle Details</h3>
      {showValidationErrors && vehicle.make === "" ? <div className="errorMessage">*This field is required.</div> : null }
      <input
        type="text"
        placeholder="Make"
        name="make"
        value={vehicle.make}
        onChange={handleInputChange('make')}
      />
      {showValidationErrors && vehicle.model === "" ? <div className="errorMessage">*This field is required.</div> : null }
      <input
        type="text"
        placeholder="Model"
        name="model"
        value={vehicle.model}
        onChange={handleInputChange('model')}
        />
      {showValidationErrors && vehicle.color === "" ? <div className="errorMessage">*This field is required.</div> : null }
      <input
        type="text"
        placeholder="Color"
        name="color"
        value={vehicle.color}
        onChange={handleInputChange('color')}
      />
    </div>
  );
}
