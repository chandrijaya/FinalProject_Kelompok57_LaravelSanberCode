<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Pertanyaan;
use App\Jawaban;
use App\VoteJawaban;
use App\VotePertanyaan;

class PertanyaanController extends Controller {
    public function __construct() {
        $this->middleware('auth')->except(['index']); //index tidak diberi authentication
    }

    // Menampilkan semua pertanyaan dengan eloquent
    public function index() {
        $pertanyaan = Pertanyaan::all();
        $vote = new VotePertanyaan;
        return view('pertanyaan.index', compact('pertanyaan', 'vote'));
    }

    // Menampilkan pertanyaan tertentu
    public static function show($id) {
        $jawaban = Jawaban::where('pertanyaan_id', $id)->get();
        $pertanyaan = Pertanyaan::find($id);
        return view('pertanyaan.index_by_id', ['daftar_jawaban' => $jawaban, 'pertanyaan' => $pertanyaan]);
    }

    // Buat pertanyaan
    public function create() {
        return view('pertanyaan.form');
    }
    public function store(Request $request) {
        $data = $request->all();
        unset($data['_token']);
        $pertanyaan = Pertanyaan::create([
            'judul' => $data['judul'],
            'isi' => $data['isi'],
            'user_id' => Auth::id()
        ]);
        return redirect('/pertanyaan'); 
    }

    // Edit Pertanyaan
    public function edit($id) {
        $pertanyaan = Pertanyaan::find($id);
        return view('pertanyaan.form_update', compact('pertanyaan'));
    }
    public function update($id, Request $request) {
        $data = $request->all();
        unset($data['_token']);
        $pertanyaan = Pertanyaan::where('id', $id)
            ->update([
            'judul' => $data['judul'],
            'isi' => $data['isi']
        ]);
        return redirect('/pertanyaan'); 
    }

    // Hapus pertanyaan
    public function delete($id) {
        $vote_pertanyaan_removed = VotePertanyaan::where('pertanyaan_id', $id)->forceDelete();
        $jawaban_removed = Jawaban::where('pertanyaan_id', $id)->forceDelete();
        $pertanyaan_removed = Pertanyaan::where('id', $id)->forceDelete();
        return redirect('/pertanyaan');
    }

    // Upvote pertanyaan
    public function vote(Request $request) {
        $pertanyaan_id = $request['pertanyaan_id'];
        $is_vote = $request['isVote'] === 'true';
        if ($is_vote == 1) {
            $is_vote = 1;
        } else {
            $is_vote = -1;
        }
        echo $is_vote;
        $update = false;
        $pertanyaan = Pertanyaan::find($pertanyaan_id);
        if (!$pertanyaan) {
            return null;
        }
        $user = Auth::user();
        $vote = $user->vote_pertanyaan()->where('pertanyaan_id', $pertanyaan_id)->first();
        if ($vote) {
            $already_vote = $vote->value;
            $update = true;
            if ($already_vote == $is_vote) {
                $vote->delete();
                return null;
            }
        } else {
            $vote = new VotePertanyaan();
        }
        $vote->value = $is_vote;
        $vote->user_id = $user->id;
        $vote->pertanyaan_id = $pertanyaan->id;
        if ($update) {
            $vote->update();
        } else {
            $vote->save();
        }
        return null;
    }
}
