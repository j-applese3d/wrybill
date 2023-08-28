
class App
{
    initialize(songs, div) {
        this.div = div;
        this.songs = songs;
        this.player = new AudioInterface();
        this.draw();
    }
}
