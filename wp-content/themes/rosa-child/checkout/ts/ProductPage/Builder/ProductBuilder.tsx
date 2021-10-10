import React, { useCallback, useState, useEffect, useRef } from 'react';
import includes from 'lodash/includes';
import map from 'lodash/map';
import filter from 'lodash/filter';
import find from 'lodash/find';
import { OloRestaurantMenuProduct, OloRestaurantMenuProductModifier, OloBasketNewProductChoice } from '../../OloAPI';
import { NavigationBar, SectionPositionTracker, ErrorPage } from '../../Components';
import { isValidExtraModifier, getNoHiddeModifier } from '../../Utilities/modelHelper';

import { ProductPageErrors } from '../ProductPage';
import { ModifierSelector } from './ModifierSelector';
import { ToggleMobileBag } from './ToggleMobileBag';

interface ProductBuilderProps {
  product: OloRestaurantMenuProduct;
  errors: ProductPageErrors;
  selectedChoices: OloBasketNewProductChoice[];
  skippedModifiers: string[];
  specialinstructions: string;
  addChoice: (choiceId: string) => unknown;
  setModifierSkipped: (modifierId: string, skipped: boolean) => unknown;
  setSpecialinstructions: (specialinstructions: string) => unknown;
  openExtraChoices: (modifierId: string) => unknown;
  openChoiceInfo: (modifierId: string) => unknown;
  toggleMobileBag: () => unknown;
}

export const ProductBuilder = ({
  product,
  errors,
  selectedChoices,
  skippedModifiers,
  specialinstructions,
  addChoice,
  setModifierSkipped,
  setSpecialinstructions,
  openExtraChoices,
  openChoiceInfo,
  toggleMobileBag,
}: ProductBuilderProps) => {
  const [noHiddenModifiers] = useState<OloRestaurantMenuProductModifier[]>(product.modifiers ? getNoHiddeModifier(product.modifiers) : []);

  const sectionsRef = useRef<SectionPositionTracker<{ id: string; name: string }>>(null);
  const [currentModifierSection, setCurrentModifierSection] = useState<{ id: string; name: string } | undefined>(
    noHiddenModifiers.length > 0 ?
    {
      id: noHiddenModifiers[0].id,
      name: noHiddenModifiers[0].description,
    } :
    undefined
  );

  const extraModifiers = filter(product.modifiers, isValidExtraModifier);
  const sectionsData = map(noHiddenModifiers, modifier => {
    return { id: modifier.id, name: modifier.description };
  });

  const handleSpecialinstructionsChange = useCallback(
    (event: React.ChangeEvent<HTMLTextAreaElement>) => {
      setSpecialinstructions(event.target.value);
    },
    [setSpecialinstructions]
  );

  const updateSection = useCallback(
    (modifierSection: { id: string; name: string }) => {
      const sectionsElem = sectionsRef.current;
      if (!sectionsElem) {
        return;
      }
      setCurrentModifierSection(modifierSection);
      sectionsElem.scrollToSection(modifierSection);
    },
    [sectionsRef, setCurrentModifierSection]
  );

  const onSectionChange = useCallback(
    (modifierSection: { id: string; name: string }) => {
      setCurrentModifierSection(modifierSection);
    },
    [setCurrentModifierSection]
  );

  //update errors
  useEffect(() => {
    const scrollToModifierIdWithError = errors.scrollToModifierIdWithError;
    if(!scrollToModifierIdWithError) {
      return;
    }

    const newSectionError = find(noHiddenModifiers, { id: scrollToModifierIdWithError });
    if (!newSectionError) {
      return;
    }

    updateSection({ id: newSectionError.id, name: newSectionError.description });
  }, [noHiddenModifiers, errors, updateSection]);

  return (
    <React.Fragment>
      <NavigationBar sectionsData={sectionsData} currentSection={currentModifierSection} updateSection={updateSection} menuLabel="Selection">
        <ToggleMobileBag product={product} selectedChoices={selectedChoices} toggleMobileBag={toggleMobileBag} />
      </NavigationBar>
      {
        noHiddenModifiers.length > 0 ?
        (<div className="productBuilder">
            <div className="modifiers">
              <SectionPositionTracker ref={sectionsRef} sectionsData={sectionsData} currentSection={currentModifierSection!} onSectionChange={onSectionChange}>
                {map(noHiddenModifiers, modifier => (
                  <ModifierSelector
                    key={modifier.id}
                    modifier={modifier}
                    error={errors.modifiers[modifier.id]}
                    selectedChoices={selectedChoices}
                    skipped={includes(skippedModifiers, modifier.id)}
                    extraModifier={find(extraModifiers, { extraOf: modifier.id })}
                    addChoice={addChoice}
                    setModifierSkipped={setModifierSkipped}
                    openExtraChoices={openExtraChoices}
                    openChoiceInfo={openChoiceInfo}
                  />
                ))}
              </SectionPositionTracker>
            </div>
            <textarea value={specialinstructions} onChange={handleSpecialinstructionsChange} placeholder="Notes" />
        </div>)
        : <ErrorPage error={new Error("This product is not customizable")}/>
      }
    </React.Fragment>
  );
};
