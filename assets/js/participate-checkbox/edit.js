/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ExternalLink } from '@wordpress/components';
import { CheckboxControl } from '@woocommerce/blocks-checkout';
import { ADMIN_URL } from '@woocommerce/settings';

export const Edit = ({ attributes, setAttributes }) => {
	const { text } = attributes;
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody
					title={ __('MLP Aktion', 'mlp-aktion' ) }
				>
					<ExternalLink
						href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=mlp_aktion` }
					>
						{ __(
							'MLP Aktion konfigurieren',
							'mlp-aktion'
						) }
					</ExternalLink>
				</PanelBody>
			</InspectorControls>
			<div className="mlp-aktion-editor-checkboxes">
				<CheckboxControl
                    id="mlp-aktion-participation"
                    checked={ false }
                    disabled={ true }
                    onChange={ () => {} }
                >
                    <span>
                        Diese Checkbox wird dem Kunden angezeigt, wenn eine Aktion konfiguriert ist.
                    </span>
                </CheckboxControl>
			</div>
		</div>
	);
};

export const Save = ({ attributes }) => {
	const { text } = attributes;

	return (
		<div {...useBlockProps.save()}>

		</div>
	);
};
