<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Address::class);

        $addresses = Address::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('enderecos.index', compact('addresses'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Address::class);

        // se já tem endereço, vai para editar
        $existing = Address::where('user_id', $request->user()->id)->first();
        if ($existing) {
            return redirect()->route('enderecos.edit', $existing)
                ->with('status', 'Você já possui um endereço. Edite-o abaixo.');
        }

        return view('enderecos.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Address::class);

        $userId = $request->user()->id;

        $data = $request->validate([
            'user_id'    => ['required', Rule::in([$userId])], // só pode salvar para si
            'logradouro' => ['required','string','max:255'],
            'numero'     => ['required','string','max:50'],
            'complemento'=> ['nullable','string','max:255'],
            'bairro'     => ['required','string','max:120'],
            'cidade'     => ['required','string','max:120'],
            'estado'     => ['required','string','size:2'],
            'cep'        => ['required','regex:/^\d{5}-?\d{3}$/'],
        ], [
            'user_id.in' => 'O endereço só pode ser criado para o próprio usuário.',
            'cep.regex'  => 'Informe um CEP válido (00000-000).',
        ]);

        // impede duplicidade (em caso de corrida)
        if (Address::where('user_id', $userId)->exists()) {
            return redirect()->route('enderecos.index')
                ->with('error', 'Você já possui um endereço cadastrado.');
        }

        Address::create($data);

        return redirect()->route('enderecos.index')
            ->with('success', 'Endereço cadastrado com sucesso!');
    }

    public function edit(Request $request, Address $endereco)
    {
        $this->authorize('view', $endereco);

        return view('enderecos.edit', ['address' => $endereco]);
    }

    public function update(Request $request, Address $endereco)
    {
        $this->authorize('update', $endereco);

        $userId = $request->user()->id;

        $data = $request->validate([
            'user_id'    => ['required', Rule::in([$userId])],
            'logradouro' => ['required','string','max:255'],
            'numero'     => ['required','string','max:50'],
            'complemento'=> ['nullable','string','max:255'],
            'bairro'     => ['required','string','max:120'],
            'cidade'     => ['required','string','max:120'],
            'estado'     => ['required','string','size:2'],
            'cep'        => ['required','regex:/^\d{5}-?\d{3}$/'],
        ], [
            'user_id.in' => 'O endereço só pode ser atualizado pelo próprio usuário.',
            'cep.regex'  => 'Informe um CEP válido (00000-000).',
        ]);

        $endereco->update($data);

        return redirect()->route('enderecos.index')->with('success', 'Endereço atualizado!');
    }

    public function destroy(Request $request, Address $endereco)
    {
        $this->authorize('delete', $endereco);

        $endereco->delete();

        return redirect()->route('enderecos.index')->with('success', 'Endereço excluído.');
    }
}
