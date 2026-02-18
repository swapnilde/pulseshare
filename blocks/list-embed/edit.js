import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	RadioControl,
	ToggleControl,
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
export default class listEmbedEdit extends Component {
	componentDidMount() {
		const { attributes, setAttributes, clientId } = this.props;
		const { blockID, episodesArray } = attributes;

		if (!blockID) {
			setAttributes({ blockID: `list-embed-${clientId}` });
		}

		if (0 === episodesArray.length) {
			this.initEpisodes();
		}

		// Fetch episodes via the server-side REST API proxy (credentials stay server-side).
		apiFetch({ path: '/pulseshare/v1/episodes' })
			.then((episodes) => {
				setAttributes({
					episodesArray: episodes,
				});
			})
			.catch((error) => {
				console.error('PulseShare: Failed to fetch episodes', error);
			});
	}

	initEpisodes() {
		const { setAttributes } = this.props;
		setAttributes({
			episodesArray: [],
		});
	}

	render() {
		const { attributes, setAttributes, className } = this.props;
		const {
			blockID,
			episodesArray,
			displayType,
			currentEpisode,
			isVideo,
			height,
			width,
		} = attributes;

		const classes = classnames(className, 'list-embed');

		const video = isVideo ? 'video' : '';

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
								help="Select the display type for the episode."
								selected={displayType ? displayType : 'full'}
								options={[
									{ label: 'Full Show', value: 'full' },
									{
										label: 'Single Episode',
										value: 'single',
									},
								]}
								onChange={(type) => {
									setAttributes({ displayType: type });
								}}
							/>

							{displayType === 'single' && (
								<SelectControl
									__nextHasNoMarginBottom
									label={__(
										'Select Episode',
										'pulseshare'
									)}
									help="Selected episode will be displayed in the frontend."
									value={
										currentEpisode
											? currentEpisode.id
											: episodesArray[0].id
									}
									options={episodesArray.map(
										(episode) => {
											return {
												label: episode.name,
												value: episode.id,
											};
										}
									)}
									onChange={(id) => {
										setAttributes({
											currentEpisode: episodesArray.find(
												(episode) => episode.id === id
											),
										});
									}}
								/>
							)}

							{displayType === 'single' && (
								<ToggleControl
									__nextHasNoMarginBottom
									checked={isVideo ? isVideo : false}
									help={__(
										'Enable this option if this episode is a video.',
										'pulseshare'
									)}
									label={__(
										'Is this a video episode?',
										'pulseshare'
									)}
									onChange={(state) => {
										setAttributes({ isVideo: state });
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
							{displayType === 'single' &&
								!currentEpisode.id && (
									<div className="notice notice-info alt">
										<p>
											<i>
												{__(
													'Please select an episode from the block settings.',
													'pulseshare'
												)}
											</i>
										</p>
									</div>
								)}

							{displayType === 'single' && currentEpisode.id && (
								<iframe
									id={'sfwe-episode-' + currentEpisode.id}
									frameBorder="0"
									allowFullScreen=""
									allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
									loading="lazy"
									width={width ? width : '100%'}
									height={height ? height : '200'}
									src={
										'https://open.spotify.com/embed/episode/' +
										currentEpisode.id +
										'/' +
										video
									}
								></iframe>
							)}
							{displayType === 'full' && (
								<iframe
									id={
										'sfwe-show-' +
										PulseShareAdminVars.pulseshare_options.show_id
									}
									frameBorder="0"
									allowFullScreen=""
									allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
									loading="lazy"
									width={width ? width : '100%'}
									height={height ? height : '200'}
									src={
										'https://open.spotify.com/embed/show/' +
										PulseShareAdminVars.pulseshare_options.show_id
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
