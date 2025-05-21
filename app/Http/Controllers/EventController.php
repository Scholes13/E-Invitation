<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class EventController extends Controller
{
    public function index()
    {
        $event = Event::where('id_event', 1)->first();
        return view("event.index", compact('event'));
    }

    public function setEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:100',
            'type' => 'required|string|min:3|max:150',
            'place' => 'required|string|min:3|max:200',
            'location' => 'required|string|min:3|max:200',
            'start' => 'required|date|after:today',
            'end' => 'required|date|after:start',
            'image' => 'nullable|image|max:512|mimes:jpg,jpeg,png',
            'image_left_event' => 'nullable|image|max:1024|mimes:jpg,jpeg,png',
            'image_right_event' => 'nullable|image|max:1024|mimes:jpg,jpeg,png',
            'image_bg_event' => 'nullable|image|max:1536|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $event = Event::where(['id_event' => 1])->first();
        // print_r($event->image_event);

        if ($request->hasFile('image')) {
            $imageName = 'top'.$event->id_event.'-'.time().'.'.$request->file('image')->extension();
            File::ensureDirectoryExists(public_path('img/event'));
            $request->file('image')->move(public_path('img/event/'), $imageName);
            if($event->image_event != "") {
                if (file_exists(public_path('img/event/' . $event->image_event))) {
                    unlink(public_path('img/event/' . $event->image_event));
                }
            }
            // Storage::disk('public')->delete('img/event/'.$event->image_event);
            // Storage::putFileAs('public/img/event', $request->file('image'), $imageName);
            // $request->file('image')->storeAs('public/img/event/', $imageName);
        }

        if ($request->hasFile('image_left_event')) {
            $imgLeftName = 'left'.$event->id_event.'-'.time().'.'.$request->file('image_left_event')->extension();
            File::ensureDirectoryExists(public_path('img/event'));
            $request->file('image_left_event')->move(public_path('img/event/'), $imgLeftName);
            if($event->image_left_event != "") {
                if (file_exists(public_path('img/event/' . $event->image_left_event))) {
                    unlink(public_path('img/event/' . $event->image_left_event));
                }
            }
        }
        if ($request->hasFile('image_right_event')) {
            $imgRightName = 'right'.$event->id_event.'-'.time().'.'.$request->file('image_right_event')->extension();
            File::ensureDirectoryExists(public_path('img/event'));
            $request->file('image_right_event')->move(public_path('img/event/'), $imgRightName);
            if($event->image_right_event != "") {
                if (file_exists(public_path('img/event/' . $event->image_right_event))) {
                    unlink(public_path('img/event/' . $event->image_right_event));
                }
            }
        }
        if ($request->hasFile('image_bg_event')) {
            $imgBgName = 'bg'.$event->id_event.'-'.time().'.'.$request->file('image_bg_event')->extension();
            File::ensureDirectoryExists(public_path('img/event'));
            $request->file('image_bg_event')->move(public_path('img/event/'), $imgBgName);
            if($event->image_bg_event != "") {
                if (file_exists(public_path('img/event/' . $event->image_bg_event))) {
                    unlink(public_path('img/event/' . $event->image_bg_event));
                }
            }
        }

        $event->name_event        = $request->name;
        $event->type_event        = $request->type;
        $event->place_event       = $request->place;
        $event->location_event    = $request->location;
        $event->start_event       = $request->start;
        $event->end_event         = $request->end;
        $event->information_event = $request->information;
        if ($request->hasFile('image')) {
            $event->image_event = $imageName;
        }
        if ($request->hasFile('image_left_event')) {
            $event->image_left_event = $imgLeftName;
        }
        if ($request->hasFile('image_right_event')) {
            $event->image_right_event = $imgRightName;
        }
        if ($request->hasFile('image_bg_event')) {
            $event->image_bg_event = $imgBgName;
        }
        $event->color_text_event = $request->color_text_event;
        $event->color_bg_event = $request->color_bg_event;
        $event->image_top_status = $request->image_top_status ?? 0;
        $event->image_left_status = $request->image_left_status ?? 0;
        $event->image_right_status = $request->image_right_status ?? 0;
        $event->image_bg_status = $request->image_bg_status ?? 0;
        $event->save();

        return redirect('event')->with("success", "Data berhasil diupdate");

    }

}
