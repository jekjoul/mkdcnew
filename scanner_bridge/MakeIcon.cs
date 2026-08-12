using System;
using System.Drawing;
using System.Drawing.Imaging;
using System.IO;
using System.Collections.Generic;

namespace IconGenerator
{
    class Program
    {
        static void Main(string[] args)
        {
            if (args.Length < 2) return;
            string inputImg = args[0];
            string outputIco = args[1];

            int[] sizes = new int[] { 16, 32, 48, 64, 128, 256 };

            using (Bitmap orig = new Bitmap(inputImg))
            {
                using (MemoryStream stream = new MemoryStream())
                {
                    BinaryWriter bw = new BinaryWriter(stream);

                    bw.Write((ushort)0);
                    bw.Write((ushort)1);
                    bw.Write((ushort)sizes.Length);

                    int offset = 6 + (sizes.Length * 16);
                    List<byte[]> imageBuffers = new List<byte[]>();

                    foreach (int sz in sizes)
                    {
                        using (Bitmap resized = new Bitmap(sz, sz, PixelFormat.Format32bppArgb))
                        {
                            using (Graphics g = Graphics.FromImage(resized))
                            {
                                g.InterpolationMode = System.Drawing.Drawing2D.InterpolationMode.HighQualityBicubic;
                                g.SmoothingMode = System.Drawing.Drawing2D.SmoothingMode.HighQuality;
                                g.DrawImage(orig, 0, 0, sz, sz);
                            }

                            // Transparency processing for outer black background
                            double cx = (sz - 1) / 2.0;
                            double cy = (sz - 1) / 2.0;
                            double maxR = sz / 2.0;

                            for (int y = 0; y < sz; y++)
                            {
                                for (int x = 0; x < sz; x++)
                                {
                                    Color c = resized.GetPixel(x, y);
                                    double dx = x - cx;
                                    double dy = y - cy;
                                    double dist = Math.Sqrt(dx * dx + dy * dy);

                                    // Outer background removal & smooth circular alpha blending
                                    if (dist > maxR * 0.965)
                                    {
                                        resized.SetPixel(x, y, Color.FromArgb(0, c.R, c.G, c.B));
                                    }
                                    else if (dist > maxR * 0.91 && c.R < 35 && c.G < 35 && c.B < 35)
                                    {
                                        double alphaFactor = (maxR * 0.965 - dist) / (maxR * 0.055);
                                        if (alphaFactor < 0) alphaFactor = 0;
                                        if (alphaFactor > 1) alphaFactor = 1;
                                        int newAlpha = (int)(c.A * alphaFactor);
                                        resized.SetPixel(x, y, Color.FromArgb(newAlpha, c.R, c.G, c.B));
                                    }
                                }
                            }

                            int bmpHeader = 40;
                            int pixelSize = sz * sz * 4;
                            int maskSize = ((sz + 31) / 32) * 4 * sz;

                            using (MemoryStream imgMs = new MemoryStream())
                            {
                                BinaryWriter imgBw = new BinaryWriter(imgMs);
                                imgBw.Write((uint)40);
                                imgBw.Write((int)(sz >= 256 ? 0 : sz));
                                imgBw.Write((int)(sz >= 256 ? 0 : sz * 2));
                                imgBw.Write((ushort)1);
                                imgBw.Write((ushort)32);
                                imgBw.Write((uint)0);
                                imgBw.Write((uint)(pixelSize + maskSize));
                                imgBw.Write((int)0);
                                imgBw.Write((int)0);
                                imgBw.Write((uint)0);
                                imgBw.Write((uint)0);

                                for (int y = sz - 1; y >= 0; y--)
                                {
                                    for (int x = 0; x < sz; x++)
                                    {
                                        Color c = resized.GetPixel(x, y);
                                        imgBw.Write(c.B);
                                        imgBw.Write(c.G);
                                        imgBw.Write(c.R);
                                        imgBw.Write(c.A);
                                    }
                                }

                                imgBw.Write(new byte[maskSize]);

                                byte[] imgData = imgMs.ToArray();
                                imageBuffers.Add(imgData);

                                bw.Write((byte)(sz >= 256 ? 0 : sz));
                                bw.Write((byte)(sz >= 256 ? 0 : sz));
                                bw.Write((byte)0);
                                bw.Write((byte)0);
                                bw.Write((ushort)1);
                                bw.Write((ushort)32);
                                bw.Write((uint)imgData.Length);
                                bw.Write((uint)offset);

                                offset += imgData.Length;
                            }
                        }
                    }

                    foreach (byte[] buf in imageBuffers)
                    {
                        bw.Write(buf);
                    }

                    File.WriteAllBytes(outputIco, stream.ToArray());
                }
            }
            Console.WriteLine("Multi ICO OK");
        }
    }
}