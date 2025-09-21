{{-- resources/views/layouts/footer.blade.php --}}
<footer class="bg-gray-50 text-gray-700 border-t mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Links institucionais --}}
        <div class="flex flex-wrap justify-center gap-6 mb-6 text-sm">
            <a href="#" class="hover:text-gray-900 hover:underline transition-colors duration-200">Trabalhe conosco</a>
            <a href="#" class="hover:text-gray-900 hover:underline transition-colors duration-200">Termos e condições</a>
            <a href="#" class="hover:text-gray-900 hover:underline transition-colors duration-200">Promoções</a>
            <a href="#" class="hover:text-gray-900 hover:underline transition-colors duration-200">Privacidade</a>
            <a href="#" class="hover:text-gray-900 hover:underline transition-colors duration-200">Acessibilidade</a>
            <a href="#" class="hover:text-gray-900 hover:underline transition-colors duration-200">Contato</a>
            <a href="#" class="hover:text-gray-900 hover:underline transition-colors duration-200">Programa de Afiliados</a>
            <a href="#" class="hover:text-gray-900 hover:underline transition-colors duration-200">Lista de presentes</a>
        </div>

        {{-- Linha separadora --}}
        <div class="border-t border-gray-200 mb-4"></div>

        {{-- Informações legais e direitos autorais --}}
        <div class="text-center text-xs text-gray-500 leading-relaxed space-y-1">
            {{-- Direitos autorais das imagens em negrito (Português) --}}
            <p class="font-bold">
                Produtos mostrados neste site são do 
                <a href="https://cakemehometonight.com/" target="_blank" class="hover:underline text-gray-600">
                    Cake Me Home Tonight
                </a> 
                e são usados apenas como exemplo para fins de estudo.
            </p>

            {{-- Direitos autorais das imagens em negrito (Inglês) --}}
            <p class="font-bold">
                Products shown on this site belong to 
                <a href="https://cakemehometonight.com/" target="_blank" class="hover:underline text-gray-600">
                    Cake Me Home Tonight
                </a> 
                and are used only as examples for study purposes.
            </p>

            {{-- Copyright principal --}}
            <p>© {{ date('Y') }} MinhaLoja.com.br LTDA. Todos os direitos reservados.</p>
            <p>CNPJ nº 12.345.678/0001-99 · Rua Exemplo, nº 123, Centro – São Paulo/SP – CEP 01000-000</p>
            <p>Empresa fictícia sem fins comerciais</p>
        </div>
    </div>
</footer>
