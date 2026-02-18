import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	RadioControl,
	PanelBody,
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
const { Component } = wp.element;

import classnames from 'classnames';

import './editor.scss';

import apiFetch from '@wordpress/api-fetch';

/**
 * The edit function describes the structure of your block in the context of the
 * editor.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {JSX} Element to render.
 */
export default class albumEmbedEdit extends Component {
	componentDidMount() {
		const { attributes, setAttributes, clientId } = this.props;
		const { blockID, albumArray } = attributes;

		if (!blockID) {
			setAttributes({ blockID: `album-embed-${clientId}` });
		}

		if (0 === albumArray.length) {
			this.initAlbum();
		}

		// Fetch tracks via the server-side REST API proxy (credentials stay server-side).
		apiFetch({ path: '/pulseshare/v1/tracks' })
			.then((tracks) => {
				setAttributes({
					albumArray: tracks,
				});
			})
			.catch((error) => {
				console.error('PulseShare: Failed to fetch tracks', error);
			});
	}

	initAlbum() {
		const { setAttributes } = this.props;
		setAttributes({
			albumArray: [],
		});
	}

	render() {
		const { attributes, setAttributes, className } = this.props;
		const {
			blockID,
			albumArray,
			displayType,
			currentTrack,
			height,
			width,
		} = attributes;

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
											: albumArray[0].id
									}
									options={albumArray.map((episode) => {
										return {
											label: episode.name,
											value: episode.id,
										};
									})}
									onChange={(id) => {
										setAttributes({
											currentTrack: albumArray.find(
												(episode) => episode.id === id
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
							{displayType === 'single' && !currentTrack.id && (
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

							{displayType === 'single' && currentTrack.id && (
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
										currentTrack.id
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
											.album_id
									}
								></iframe>
							)}
						</div>
					</div>
				</div>
			</>
		);
	}
}
