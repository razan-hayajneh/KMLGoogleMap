<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateKmlFileRequest;
use App\Http\Requests\UpdateKmlFileRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\KmlFile;
use App\Repositories\KmlFileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;

class KmlFileController extends AppBaseController
{
    /** @var KmlFileRepository $kmlFileRepository*/
    private $kmlFileRepository;

    public function __construct(KmlFileRepository $kmlFileRepo)
    {
        $this->kmlFileRepository = $kmlFileRepo;
    }

    /**
     * Display a listing of the KmlFile.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $kmlFile = $this->kmlFileRepository->whereUserId($user->id);
        if ($kmlFile) {
            $kmlContent = Storage::get($kmlFile['file_path']);
            $xml = simplexml_load_string($kmlContent);
            $placeMarks = $xml->Document->Placemark;
            $defaultCoordinates = [];
            foreach ($placeMarks as $placeMark) {
                $name = (string) $placeMark->name;
                $coordinates = (string) $placeMark->Point->coordinates;
                list($lng, $lat) = $coordinates ? explode(',', $coordinates) : [-73.984939, 40.758874];
                $defaultCoordinates = [
                    'name' => $name,
                    'latitude' => $lat,
                    'longitude' => $lng,
                ];
                break;
            }
            return view('kmlFiles.index')->with(['kmlContent' => $kmlContent, 'defaultCoordinates' => $defaultCoordinates]);
        }

        return view('kmlFiles.index');
    }

    /**
     * Store a newly created KmlFile in storage.
     */
    public function store(CreateKmlFileRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $kmlFile = $this->kmlFileRepository->whereUserId(Auth::user()->id);
        if (array_key_exists('file_path', $request->all()) && $request->file('file_path')->isValid()) {
            $file = $request->file('file_path');
            $input['file_path'] = $file->store('public/kml');
            if (!empty($kmlFile)) {
                if (Storage::exists($kmlFile->file_path))
                    Storage::delete($kmlFile->file_path);
                $kmlFile->update($input);
            } else {
                $kmlFile = $this->kmlFileRepository->create($input);
            }
            Flash::success('Kml File saved successfully.');
            return redirect(route('kmlFiles.index'));
        }
        return redirect()->back()->with(['error', 'File upload failed.']);
    }

    /**
     * Remove the specified KmlFile from storage.
     *
     * @throws \Exception
     */
    public function destroy()
    {
        $kmlFile = $this->kmlFileRepository->whereUserId(Auth::user()->id);
        if (empty($kmlFile)) {
            Flash::error('Kml File not found');

            return redirect(route('kmlFiles.index'));
        }

        $kmlFile->delete();

        Flash::success('Kml File deleted successfully.');

        return redirect(route('kmlFiles.index'));
    }
}
