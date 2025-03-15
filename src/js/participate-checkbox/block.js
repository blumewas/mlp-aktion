/**
 * External dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { CheckboxControl, TextInput } from '@woocommerce/blocks-checkout';
import { getSetting } from '@woocommerce/settings';
import { VALIDATION_STORE_KEY } from '@woocommerce/block-data';
import { useSelect, useDispatch } from '@wordpress/data';
import clsx from 'clsx';

const { optinCheckboxLabel, pariticipationTermsUrl, phoneNumberRequired, aktionActive, advancedWrapperClass } = getSetting( 'mlp-aktion_data', '' );

const replaceParticipationTerms = (text) => {
    const regex = /\{participation_terms\}(.*?)\{\/participation_terms\}/g;

    return text.split(regex).map((part, index) => {
        if (index % 2 === 1) {
            // This is the matched text inside {participation_terms}
            return (
                <a key={index} href={pariticipationTermsUrl}>
                    {part}
                </a>
            );
        }
        return part; // Return normal text
    });
};

const Block = ( { children, checkoutExtensionData } ) => {
	const [ checked, setChecked ] = useState( false );
    const [ phone, setPhone ] = useState( false );

	const { setExtensionData } = checkoutExtensionData;

    // Error
    const validationErrorId = 'mlp-aktion';
    const { setValidationErrors, clearValidationError } = useDispatch( VALIDATION_STORE_KEY );

    const error = useSelect( ( select ) => {
        return select( VALIDATION_STORE_KEY ).getValidationError(
            validationErrorId
        );
    } );

    const hasError = !! ( error?.message && ! error?.hidden );

    const getBillingPhoneNumber = () => {
        const billingPhoneInput = document.querySelector('#billing-phone');

        return billingPhoneInput ? billingPhoneInput.value : null;
    };

	useEffect( () => {
		setExtensionData( 'mlp-aktion', 'optin', checked );
		if ( ! checked ) {
			// Clear phone input
            setPhone(null);

			return;
		}

		// Fill phone input with billing phone when shown
        const billingPhone = getBillingPhoneNumber();

        if (billingPhone) {
            setPhone(billingPhone);
        }
	}, [
		// clearValidationError,
		// setValidationErrors,
		checked,
		setExtensionData,
        setPhone,
	] );

    useEffect( () => {
        setExtensionData( 'mlp-aktion', 'contact_phone', phone );

        // If the checkbox is not checked or the phone number is not required post
        if (! checked || ! phoneNumberRequired ) {
            return;
        }

        if ( !!phone ) {
            clearValidationError( validationErrorId );
        } else if ( phoneNumberRequired ) {
            setValidationErrors( {
                [ validationErrorId ]: {
                    message: 'Die Telefonnummer ist ein Pflichtfeld.',
                    hidden: true,
                },
            } );
        }
        return () => {
            clearValidationError( validationErrorId );
        };
    }, [
        phone,
        setExtensionData,
        validationErrorId,
        clearValidationError,
        setValidationErrors,
    ]);

	return (aktionActive &&
		<>
            <div className={ clsx('mlp-aktion-checkbox-wrapper', advancedWrapperClass) }>
                <CheckboxControl
                    id="mlp-aktion-participation"
                    checked={ checked }
                    onChange={ setChecked }
                >
                    <span>
                    { replaceParticipationTerms(optinCheckboxLabel) }
                    </span>
                </CheckboxControl>

                {checked ?
                    <div className='mlp-aktion-phone-input-wrapper'>
                        <TextInput
                            id="mlp-aktion-contact-phone"
                            label={ phoneNumberRequired ? 'Telefon' : 'Telefon (optional)' }
                            help={
                                phoneNumberRequired ?
                                    'Um an der Aktion teilnehmen zu können, musst Du eine Telefonnummer angeben unter der Dich MLP erreichen kann.'
                                    : null
                            }
                            className={ hasError ? 'has-error' : ''}
                            required={ phoneNumberRequired }
                            type="tel"
                            value={ phone }
                            onChange={ setPhone }
                            autocomplete="tel"
                        >

                        </TextInput >
                    </div> : ''
                }
            </div>
		</>
	);
};

export default Block;
