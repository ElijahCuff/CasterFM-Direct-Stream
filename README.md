# CasterFM-Direct-Stream
Direct Stream Caster FM without buying the premium membership, allows automatic stream authentication for servers.   
    
---   

## Example Usage     
- Grab the desired CasterFM stream UID   
- Upload stream.php to your server    
- Enter your UID as below    

```
/stream.php?uid=541413
```

## HTML       
- Once you have your UID and stream.php setup on your server, you can use HTML5 Audio Player to play the stream as a direct MP3 file.    
```
<audio controls>
  <source src="/stream.php?uid=541413" type="audio/mpeg">
Your browser does not support the audio element.
</audio>

```
