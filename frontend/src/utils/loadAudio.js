const modules = import.meta.glob('../assets/muziek/concentratie/*.mp4');

export async function loadAllAudio() {
  const audioFiles = await Promise.all(
    Object.entries(modules).map(async ([path, importer]) => {
      const module = await importer();
      const fileName = path.split('/').pop().split('.')[0];
      return {
        name: fileName,
        src: module.default,
      };
    })
  );

  return audioFiles;
}
