<?php

namespace App\Presenters;

use Nette;
use Oli\GoogleAPI\MapAPI;
use Oli\GoogleAPI\Markers;


class HomepagePresenter extends Nette\Application\UI\Presenter
{

	private $markersFromDb = [
		  [
			  'lat' => 59.2967322,
			  'lng' => 18.0009393,
			  'title' => 'Foo',
			  'description' => 'Some description',
			  'icon' => 'point.png',
			  'waypoint' => 'start'
		  ],
		  [
			  'lat' => 59.2980245,
			  'lng' => 17.9971503,
			  'icon' => 'point2.png',
			  'waypoint' => 'end'
		  ],
		  [
			  'lat' => 59.2981078,
			  'lng' => 17.9980875
		  ],
		  [
			  'lat' => 59.2982762,
			  'lng' => 17.9970823
		  ],
		  [
			  'lat' => 59.2987638,
			  'lng' => 17.9917639,
			  'icon' => 'point.png',
			  'waypoint' => 'waypoints',
			  'title' => 'Bar',
			  'description' => '',
		  ],
		  [
			  'lat' => 59.2987649,
			  'lng' => 17.9917824
		  ],
		  [
			  'lat' => 59.2987847,
			  'lng' => 17.9917731
		  ],
		  [
			  'lat' => 59.2988498,
			  'lng' => 17.991684
		  ],
		  [
			  'lat' => 59.2988503,
			  'lng' => 17.9981593
		  ],
		  [
			  'lat' => 59.3008233,
			  'lng' => 18.0041763
		  ],
		  [
			  'lat' => 59.3014033,
			  'lng' => 18.0068793,
			  'icon' => 'point.png',
			  'waypoint' => 'waypoints'
		  ],
		  [
			  'lat' => 59.3016619,
			  'lng' => 18.0137766
		  ]
      ];


	private $map;
	private $markers;

	public function __construct(\Oli\GoogleAPI\IMapAPI $mapApi, \Oli\GoogleAPI\IMarkers $markers)
	{
		$this->map = $mapApi;
		$this->markers = $markers;
		$this->markersFromDb = Nette\Utils\ArrayHash::from($this->markersFromDb);
	}


	protected function createComponentGoogleMap()
	{
		$map = $this->map->create();

		$map->setCoordinates(array(50.250718,14.583435))
			->setZoom(4)
			->setType(MapAPI::TERRAIN);

		$markers = $this->markers->create();
		$markers->fitBounds();

		if(count($this->markersFromDb) > 10)
		{
			$markers->isMarkerClusterer();
		}

		foreach ($this->markersFromDb as $marker)
		{
			$addedMarker = $markers->addMarker(array($marker->lat, $marker->lng), Markers::DROP);
			if (array_key_exists('title', $marker))
			{
			    $addedMarker->setMessage(
					'<h1>'.$marker->title.'</h1><br />'.$marker->description, TRUE
				)->setIcon($marker->icon);
			}

			if (array_key_exists('icon', $marker))
			{
				$addedMarker->setIcon($marker->icon);
			}

			if (array_key_exists('waypoint', $marker))
			{
				// $marker->waypoint can be start, end, waypoints
				$map->setWaypoint($marker->waypoint, $addedMarker->getMarker());
			}
		}

		$map->setDirection([
			'avoidHighways' => true,
			'avoidTolls' => true,
		]);

		$map->addMarkers($markers);
		return $map;
	}

}
