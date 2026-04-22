import Alpine from 'alpinejs'

window.Alpine = Alpine
Alpine.start()

window.demoButtonClickMessage = function(event){
    event.preventDefault(); new FilamentNotification().title('Modify this button in your theme folder').icon('heroicon-o-pencil-square').iconColor('info').send()
}