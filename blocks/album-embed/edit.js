import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	RadioControl,
	PanelBody,
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import classnames from 'classnames';

import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object} props Block props.
 * @return {JSX.Element} Element to render.
 */
export default function AlbumEmbedEdit({ attributes, setAttributes, clientId, className }) {
	const {
		blockID,
		albumArray,
		displayType,
		currentTrack,
		height,
		width,
	} = attributes;

	// Set block ID on mount.
	useEffect(() => {
		if (!blockID) {
			setAttributes({ blockID: `album-embed-${clientId}` });
		}
	}, [blockID, clientId, setAttributes]);

	// Fetch tracks via the server-side REST API proxy.
	useEffect(() => {
		apiFetch({ path: '/pulseshare/v1/tracks' })
			.then((tracks) => {
				setAttributes({ albumArray: tracks });
			})
			.catch((error) => {
				console.error('PulseShare: Failed to fetch tracks', error);
			});
	}, []); // eslint-disable-line react-hooks/exhaustive-deps

	const classes = classnames(className, 'album-embed');

	return (
		<>
			<InspectorControls>
				<div className="sfwe-block-sidebar">
					<PanelBody
						title={__('Settings', 'pulseshare')}
						initialOpen={true}
					>
						<RadioControl
							label={__('Display Type', 'pulseshare')}
							help="Select the display type for the album."
							selected={displayType ? displayType : 'full'}
							options={[
								{ label: 'Full Album', value: 'full' },
								{ label: 'Single Track', value: 'single' },
							]}
							onChange={(type) => {
								setAttributes({ displayType: type });
							}}
						/>

						{displayType === 'single' && (
							<SelectControl
								__nextHasNoMarginBottom
								label={__('Select Track', 'pulseshare')}
								help="Selected track will be displayed in the frontend."
								value={
									currentTrack
										? currentTrack.id
										: albumArray[0]?.id
								}
								options={albumArray.map((track) => {
									return {
										label: track.name,
										value: track.id,
									};
								})}
								onChange={(id) => {
									setAttributes({
										currentTrack: albumArray.find(
											(track) => track.id === id
										),
									});
								}}
							/>
						)}

						<UnitControl
							__next40pxDefaultSize
							label="Height"
							onChange={(value) => {
								setAttributes({ height: value });
							}}
							units={[
								{
									a11yLabel: 'Pixels (px)',
									label: 'px',
									step: 1,
									value: 'px',
								},
								{
									a11yLabel: 'Percent (%)',
									label: '%',
									step: 1,
									value: '%',
								},
							]}
							value={height}
						/>
						<UnitControl
							__next40pxDefaultSize
							label="Width"
							onChange={(value) => {
								setAttributes({ width: value });
							}}
							units={[
								{
									a11yLabel: 'Pixels (px)',
									label: 'px',
									step: 1,
									value: 'px',
								},
								{
									a11yLabel: 'Percent (%)',
									label: '%',
									step: 1,
									value: '%',
								},
							]}
							value={width}
						/>
					</PanelBody>
				</div>
			</InspectorControls>
			<div className={classes} id={blockID}>
				<div className="container">
					<div className={'sfwe-episode'}>
						{displayType === 'single' && !currentTrack?.id && (
							<div className="notice notice-info alt">
								<p>
									<i>
										{__(
											'Please select a track from the block settings.',
											'pulseshare'
										)}
									</i>
								</p>
							</div>
						)}

						{displayType === 'single' && currentTrack?.id && (
							<iframe
								id={'sfwe-track-' + currentTrack.id}
								frameBorder="0"
								allowFullScreen=""
								allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
								loading="lazy"
								width={width ? width : '100%'}
								height={height ? height : '200'}
								src={
									'https://open.spotify.com/embed/track/' +
									currentTrack.id +
									'?utm_source=generator'
								}
							></iframe>
						)}
						{displayType === 'full' && (
							<iframe
								id={
									'sfwe-album-' +
									PulseShareAdminVars.pulseshare_options
										.album_id
								}
								frameBorder="0"
								allowFullScreen=""
								allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
								loading="lazy"
								width={width ? width : '100%'}
								height={height ? height : '380'}
								src={
									'https://open.spotify.com/embed/album/' +
									PulseShareAdminVars.pulseshare_options
										.album_id +
									'?utm_source=generator'
								}
							></iframe>
						)}
					</div>
				</div>
			</div>
		</>
	);
}
